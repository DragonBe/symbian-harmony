<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Course;

interface CourseRepositoryInterface
{
    /**
     * Find a course by its ID
     */
    public function findById(int $id): ?Course;
    
    /**
     * Find all courses
     * 
     * @return Course[]
     */
    public function findAll(): array;
    
    /**
     * Find all promoted courses
     * 
     * @return Course[]
     */
    public function findPromoted(): array;
    
    /**
     * Find courses by search term (searches in title and description)
     * 
     * @return Course[]
     */
    public function findBySearchTerm(string $searchTerm): array;
    
    /**
     * Save a course (create or update)
     */
    public function save(Course $course): Course;
    
    /**
     * Archive a course
     */
    public function archive(int $id): bool;
}