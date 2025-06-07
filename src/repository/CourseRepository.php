<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Course;
use PDO;

class CourseRepository implements CourseRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Course
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM courses 
            WHERE id = :id AND is_archived = 0
        ');
        $stmt->execute(['id' => $id]);
        
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }
        
        return Course::fromArray($data);
    }
    
    public function findAll(): array
    {
        $stmt = $this->pdo->query('
            SELECT * FROM courses 
            WHERE is_archived = 0
            ORDER BY title ASC
        ');
        
        $courses = [];
        while ($row = $stmt->fetch()) {
            $courses[] = Course::fromArray($row);
        }
        
        return $courses;
    }
    
    public function findPromoted(): array
    {
        $stmt = $this->pdo->query('
            SELECT * FROM courses 
            WHERE is_promoted = 1 AND is_archived = 0
            ORDER BY title ASC
        ');
        
        $courses = [];
        while ($row = $stmt->fetch()) {
            $courses[] = Course::fromArray($row);
        }
        
        return $courses;
    }
    
    public function findBySearchTerm(string $searchTerm): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM courses 
            WHERE (title LIKE :search OR description LIKE :search) 
            AND is_archived = 0
            ORDER BY title ASC
        ');
        
        $searchParam = '%' . $searchTerm . '%';
        $stmt->execute(['search' => $searchParam]);
        
        $courses = [];
        while ($row = $stmt->fetch()) {
            $courses[] = Course::fromArray($row);
        }
        
        return $courses;
    }
    
    public function save(Course $course): Course
    {
        $data = $course->toArray();
        
        // Remove ID for insert, or use it for update
        $id = $data['id'];
        unset($data['id']);
        
        if ($id === 0) {
            // Insert new course
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_map(fn($key) => ':' . $key, array_keys($data)));
            
            $stmt = $this->pdo->prepare("
                INSERT INTO courses ($columns)
                VALUES ($placeholders)
            ");
            
            $stmt->execute($data);
            $newId = (int) $this->pdo->lastInsertId();
            
            return $this->findById($newId);
        } else {
            // Update existing course
            $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
            
            $stmt = $this->pdo->prepare("
                UPDATE courses
                SET $setClause
                WHERE id = :id
            ");
            
            $stmt->execute(array_merge($data, ['id' => $id]));
            
            return $this->findById($id);
        }
    }
    
    public function archive(int $id): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE courses
            SET is_archived = 1, updated_at = NOW()
            WHERE id = :id
        ');
        
        $stmt->execute(['id' => $id]);
        
        return $stmt->rowCount() > 0;
    }
}