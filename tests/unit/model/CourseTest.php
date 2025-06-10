<?php
declare(strict_types=1);

namespace Tests\Unit\Model;

use App\Model\Course;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Course::class)]
class CourseTest extends TestCase
{
    public function testCreateMethodReturnsNewCourseInstance(): void
    {
        $title = 'PHP Course';
        $description = 'Learn PHP programming';
        $isPromoted = true;

        $course = Course::create($title, $description, $isPromoted);

        $this->assertInstanceOf(Course::class, $course);
        $this->assertSame(0, $course->getId());
        $this->assertSame($title, $course->getTitle());
        $this->assertSame($description, $course->getDescription());
        $this->assertSame($isPromoted, $course->isPromoted());
        $this->assertFalse($course->isArchived());
        $this->assertInstanceOf(\DateTimeImmutable::class, $course->getCreatedAt());
        $this->assertNull($course->getUpdatedAt());
    }

    public function testFromArrayMethodReturnsNewCourseInstance(): void
    {
        $data = [
            'id' => 1,
            'title' => 'PHP Course',
            'description' => 'Learn PHP programming',
            'is_promoted' => true,
            'is_archived' => false,
            'created_at' => '2023-01-01 12:00:00',
            'updated_at' => '2023-01-02 12:00:00',
        ];

        $course = Course::fromArray($data);

        $this->assertInstanceOf(Course::class, $course);
        $this->assertSame(1, $course->getId());
        $this->assertSame('PHP Course', $course->getTitle());
        $this->assertSame('Learn PHP programming', $course->getDescription());
        $this->assertTrue($course->isPromoted());
        $this->assertFalse($course->isArchived());
        $this->assertInstanceOf(\DateTimeImmutable::class, $course->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $course->getUpdatedAt());
    }

    public function testFromArrayMethodWithNullUpdatedAt(): void
    {
        $data = [
            'id' => 1,
            'title' => 'PHP Course',
            'description' => 'Learn PHP programming',
            'is_promoted' => true,
            'is_archived' => false,
            'created_at' => '2023-01-01 12:00:00',
            'updated_at' => null,
        ];

        $course = Course::fromArray($data);

        $this->assertNull($course->getUpdatedAt());
    }

    public function testToArrayMethodReturnsArrayRepresentation(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updatedAt = new \DateTimeImmutable('2023-01-02 12:00:00');

        $course = new Course(
            1,
            'PHP Course',
            'Learn PHP programming',
            true,
            false,
            $createdAt,
            $updatedAt
        );

        $array = $course->toArray();

        $this->assertIsArray($array);
        $this->assertSame(1, $array['id']);
        $this->assertSame('PHP Course', $array['title']);
        $this->assertSame('Learn PHP programming', $array['description']);
        $this->assertTrue($array['is_promoted']);
        $this->assertFalse($array['is_archived']);
        $this->assertSame('2023-01-01 12:00:00', $array['created_at']);
        $this->assertSame('2023-01-02 12:00:00', $array['updated_at']);
    }

    public function testToArrayMethodWithNullUpdatedAt(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');

        $course = new Course(
            1,
            'PHP Course',
            'Learn PHP programming',
            true,
            false,
            $createdAt,
            null
        );

        $array = $course->toArray();

        $this->assertNull($array['updated_at']);
    }

    public function testWithTitleReturnsNewInstanceWithUpdatedTitle(): void
    {
        $course = Course::create('PHP Course', 'Learn PHP programming');
        $newTitle = 'Updated PHP Course';

        $updatedCourse = $course->withTitle($newTitle);

        $this->assertNotSame($course, $updatedCourse);
        $this->assertSame($newTitle, $updatedCourse->getTitle());
        $this->assertSame('Learn PHP programming', $updatedCourse->getDescription());
        $this->assertInstanceOf(\DateTimeImmutable::class, $updatedCourse->getUpdatedAt());
    }

    public function testWithDescriptionReturnsNewInstanceWithUpdatedDescription(): void
    {
        $course = Course::create('PHP Course', 'Learn PHP programming');
        $newDescription = 'Updated description';

        $updatedCourse = $course->withDescription($newDescription);

        $this->assertNotSame($course, $updatedCourse);
        $this->assertSame('PHP Course', $updatedCourse->getTitle());
        $this->assertSame($newDescription, $updatedCourse->getDescription());
        $this->assertInstanceOf(\DateTimeImmutable::class, $updatedCourse->getUpdatedAt());
    }

    public function testWithPromotedReturnsNewInstanceWithUpdatedPromotedStatus(): void
    {
        $course = Course::create('PHP Course', 'Learn PHP programming', false);

        $updatedCourse = $course->withPromoted(true);

        $this->assertNotSame($course, $updatedCourse);
        $this->assertFalse($course->isPromoted());
        $this->assertTrue($updatedCourse->isPromoted());
        $this->assertInstanceOf(\DateTimeImmutable::class, $updatedCourse->getUpdatedAt());
    }

    public function testWithArchivedReturnsNewInstanceWithUpdatedArchivedStatus(): void
    {
        $course = Course::create('PHP Course', 'Learn PHP programming');

        $updatedCourse = $course->withArchived(true);

        $this->assertNotSame($course, $updatedCourse);
        $this->assertFalse($course->isArchived());
        $this->assertTrue($updatedCourse->isArchived());
        $this->assertInstanceOf(\DateTimeImmutable::class, $updatedCourse->getUpdatedAt());
    }
}
