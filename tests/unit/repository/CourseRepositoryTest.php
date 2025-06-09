<?php
declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Model\Course;
use App\Repository\CourseRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class CourseRepositoryTest extends TestCase
{
    private $pdo;
    private $repository;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = new CourseRepository($this->pdo);
    }

    public function testFindByIdReturnsNullWhenCourseNotFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        // Mock the PDO prepare and execute methods
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT * FROM courses'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $result = $this->repository->findById(1);

        $this->assertNull($result);
    }

    public function testFindByIdReturnsCourseWhenFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $courseData = [
            'id' => 1,
            'title' => 'PHP Course',
            'description' => 'Learn PHP programming',
            'is_promoted' => true,
            'is_archived' => false,
            'created_at' => '2023-01-01 12:00:00',
            'updated_at' => null,
        ];

        // Mock the PDO prepare and execute methods
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT * FROM courses'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn($courseData);

        $result = $this->repository->findById(1);

        $this->assertInstanceOf(Course::class, $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('PHP Course', $result->getTitle());
        $this->assertSame('Learn PHP programming', $result->getDescription());
        $this->assertTrue($result->isPromoted());
        $this->assertFalse($result->isArchived());
    }

    public function testFindAllReturnsEmptyArrayWhenNoCoursesFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        // Mock the PDO query method
        $this->pdo->expects($this->once())
            ->method('query')
            ->with($this->stringContains('SELECT * FROM courses'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindAllReturnsArrayOfCoursesWhenFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $courseData1 = [
            'id' => 1,
            'title' => 'PHP Course',
            'description' => 'Learn PHP programming',
            'is_promoted' => true,
            'is_archived' => false,
            'created_at' => '2023-01-01 12:00:00',
            'updated_at' => null,
        ];
        $courseData2 = [
            'id' => 2,
            'title' => 'JavaScript Course',
            'description' => 'Learn JavaScript programming',
            'is_promoted' => false,
            'is_archived' => false,
            'created_at' => '2023-01-02 12:00:00',
            'updated_at' => null,
        ];

        // Mock the PDO query method
        $this->pdo->expects($this->once())
            ->method('query')
            ->with($this->stringContains('SELECT * FROM courses'))
            ->willReturn($stmt);

        // Mock the fetch method to return two courses and then false
        $stmt->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls($courseData1, $courseData2, false);

        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Course::class, $result[0]);
        $this->assertInstanceOf(Course::class, $result[1]);
        $this->assertSame(1, $result[0]->getId());
        $this->assertSame(2, $result[1]->getId());
        $this->assertSame('PHP Course', $result[0]->getTitle());
        $this->assertSame('JavaScript Course', $result[1]->getTitle());
    }

    public function testFindPromotedReturnsEmptyArrayWhenNoCoursesFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        // Mock the PDO query method
        $this->pdo->expects($this->once())
            ->method('query')
            ->with($this->stringContains('SELECT * FROM courses'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $result = $this->repository->findPromoted();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindPromotedReturnsArrayOfPromotedCoursesWhenFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $courseData = [
            'id' => 1,
            'title' => 'PHP Course',
            'description' => 'Learn PHP programming',
            'is_promoted' => true,
            'is_archived' => false,
            'created_at' => '2023-01-01 12:00:00',
            'updated_at' => null,
        ];

        // Mock the PDO query method
        $this->pdo->expects($this->once())
            ->method('query')
            ->with($this->stringContains('SELECT * FROM courses'))
            ->willReturn($stmt);

        // Mock the fetch method to return one course and then false
        $stmt->expects($this->exactly(2))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls($courseData, false);

        $result = $this->repository->findPromoted();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Course::class, $result[0]);
        $this->assertSame(1, $result[0]->getId());
        $this->assertSame('PHP Course', $result[0]->getTitle());
        $this->assertTrue($result[0]->isPromoted());
    }

    public function testFindBySearchTermReturnsEmptyArrayWhenNoCoursesFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        // Mock the PDO prepare and execute methods
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT * FROM courses'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['search' => '%php%']);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $result = $this->repository->findBySearchTerm('php');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindBySearchTermReturnsArrayOfCoursesWhenFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $courseData = [
            'id' => 1,
            'title' => 'PHP Course',
            'description' => 'Learn PHP programming',
            'is_promoted' => true,
            'is_archived' => false,
            'created_at' => '2023-01-01 12:00:00',
            'updated_at' => null,
        ];

        // Mock the PDO prepare and execute methods
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT * FROM courses'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['search' => '%php%']);

        // Mock the fetch method to return one course and then false
        $stmt->expects($this->exactly(2))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls($courseData, false);

        $result = $this->repository->findBySearchTerm('php');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Course::class, $result[0]);
        $this->assertSame(1, $result[0]->getId());
        $this->assertSame('PHP Course', $result[0]->getTitle());
    }

    public function testSaveInsertsNewCourseWhenIdIsZero(): void
    {
        $course = Course::create('PHP Course', 'Learn PHP programming', true);
        $insertStmt = $this->createMock(PDOStatement::class);
        $selectStmt = $this->createMock(PDOStatement::class);
        $newCourseData = [
            'id' => 1,
            'title' => 'PHP Course',
            'description' => 'Learn PHP programming',
            'is_promoted' => true,
            'is_archived' => false,
            'created_at' => '2023-01-01 12:00:00',
            'updated_at' => null,
        ];

        // Mock the PDO prepare method to return different statements based on the SQL
        $this->pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnCallback(function ($sql) use ($insertStmt, $selectStmt) {
                if (strpos($sql, 'INSERT INTO') !== false) {
                    return $insertStmt;
                } else {
                    return $selectStmt;
                }
            });

        $insertStmt->expects($this->once())
            ->method('execute')
            ->with($this->callback(function ($data) {
                return $data['title'] === 'PHP Course' &&
                       $data['description'] === 'Learn PHP programming' &&
                       $data['is_promoted'] === true &&
                       $data['is_archived'] === false;
            }));

        $this->pdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('1');

        // Mock the findById method to return the new course
        $selectStmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $selectStmt->expects($this->once())
            ->method('fetch')
            ->willReturn($newCourseData);

        $result = $this->repository->save($course);

        $this->assertInstanceOf(Course::class, $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('PHP Course', $result->getTitle());
        $this->assertSame('Learn PHP programming', $result->getDescription());
        $this->assertTrue($result->isPromoted());
        $this->assertFalse($result->isArchived());
    }

    public function testSaveUpdatesExistingCourseWhenIdIsNotZero(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');
        $course = new Course(
            1,
            'PHP Course',
            'Learn PHP programming',
            true,
            false,
            $createdAt
        );
        $updateStmt = $this->createMock(PDOStatement::class);
        $selectStmt = $this->createMock(PDOStatement::class);
        $updatedCourseData = [
            'id' => 1,
            'title' => 'PHP Course',
            'description' => 'Learn PHP programming',
            'is_promoted' => true,
            'is_archived' => false,
            'created_at' => '2023-01-01 12:00:00',
            'updated_at' => '2023-01-02 12:00:00',
        ];

        // Mock the PDO prepare method to return different statements based on the SQL
        $this->pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnCallback(function ($sql) use ($updateStmt, $selectStmt) {
                if (strpos($sql, 'UPDATE courses') !== false) {
                    return $updateStmt;
                } else {
                    return $selectStmt;
                }
            });

        $updateStmt->expects($this->once())
            ->method('execute')
            ->with($this->callback(function ($data) {
                return $data['title'] === 'PHP Course' &&
                       $data['description'] === 'Learn PHP programming' &&
                       $data['is_promoted'] === true &&
                       $data['is_archived'] === false &&
                       $data['id'] === 1;
            }));

        // Mock the findById method to return the updated course
        $selectStmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $selectStmt->expects($this->once())
            ->method('fetch')
            ->willReturn($updatedCourseData);

        $result = $this->repository->save($course);

        $this->assertInstanceOf(Course::class, $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame('PHP Course', $result->getTitle());
        $this->assertSame('Learn PHP programming', $result->getDescription());
        $this->assertTrue($result->isPromoted());
        $this->assertFalse($result->isArchived());
        $this->assertInstanceOf(\DateTimeImmutable::class, $result->getUpdatedAt());
    }

    public function testArchiveReturnsTrueWhenCourseArchived(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        // Mock the PDO prepare and execute methods
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('UPDATE courses'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $stmt->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $result = $this->repository->archive(1);

        $this->assertTrue($result);
    }

    public function testArchiveReturnsFalseWhenCourseNotFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        // Mock the PDO prepare and execute methods
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('UPDATE courses'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 999]);

        $stmt->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);

        $result = $this->repository->archive(999);

        $this->assertFalse($result);
    }
}
