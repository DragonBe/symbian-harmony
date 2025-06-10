<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\AdminController;
use App\Model\Course;
use App\Repository\CourseRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

#[CoversClass(AdminController::class)]
class AdminControllerTest extends TestCase
{
    private $twig;
    private $courseRepository;
    private $request;
    private $response;
    private $adminController;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Twig::class);
        $this->courseRepository = $this->createMock(CourseRepositoryInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->adminController = new AdminController($this->twig, $this->courseRepository);
    }

    public function testDashboardMethodRendersTemplate(): void
    {
        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'admin/dashboard.html.twig',
                [
                    'pageTitle' => 'Admin Dashboard',
                ]
            )
            ->willReturn($this->response);

        // Call the dashboard method
        $result = $this->adminController->dashboard($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testLoginFormMethodRendersTemplate(): void
    {
        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'admin/login.html.twig',
                [
                    'pageTitle' => 'Admin Login',
                ]
            )
            ->willReturn($this->response);

        // Call the loginForm method
        $result = $this->adminController->loginForm($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testLoginMethodRedirectsToDashboard(): void
    {
        // Mock the request to return parsed body
        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['username' => 'admin', 'password' => 'password']);

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the login method
        $result = $this->adminController->login($this->request, $this->response);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testListCoursesMethodCallsRepositoryAndRendersTemplate(): void
    {
        // Mock the repository to return some courses
        $courses = ['course1', 'course2'];
        $this->courseRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($courses);

        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'admin/courses.html.twig',
                [
                    'pageTitle' => 'Manage Courses',
                    'courses' => $courses,
                ]
            )
            ->willReturn($this->response);

        // Call the listCourses method
        $result = $this->adminController->listCourses($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testAddCourseFormMethodRendersTemplate(): void
    {
        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'admin/course-form.html.twig',
                [
                    'pageTitle' => 'Add Course',
                    'isEdit' => false,
                ]
            )
            ->willReturn($this->response);

        // Call the addCourseForm method
        $result = $this->adminController->addCourseForm($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testAddCourseMethodWithValidDataSavesCourseAndRedirects(): void
    {
        // Mock the request to return parsed body with valid data
        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'title' => 'PHP Course',
                'description' => 'Learn PHP programming',
                'is_promoted' => '1'
            ]);

        // Mock the repository save method to return the saved course
        $this->courseRepository->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($course) {
                // Create a new course with ID 1 to simulate the database setting an ID
                $createdAt = new \DateTimeImmutable();
                return new Course(
                    1,
                    $course->getTitle(),
                    $course->getDescription(),
                    $course->isPromoted(),
                    $course->isArchived(),
                    $createdAt
                );
            });

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin/courses')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the addCourse method
        $result = $this->adminController->addCourse($this->request, $this->response);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testAddCourseMethodWithInvalidDataRedirectsToAddForm(): void
    {
        // Mock the request to return parsed body with invalid data (empty title)
        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'title' => '',
                'description' => 'Learn PHP programming',
                'is_promoted' => '1'
            ]);

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin/courses/add')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the addCourse method
        $result = $this->adminController->addCourse($this->request, $this->response);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testEditCourseFormMethodWithValidCourseId(): void
    {
        $courseId = 1;
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');
        $course = new Course(
            $courseId,
            'PHP Course',
            'Learn PHP programming',
            false,
            false,
            $createdAt
        );

        // Mock the repository to return a course
        $this->courseRepository->expects($this->once())
            ->method('findById')
            ->with($courseId)
            ->willReturn($course);

        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'admin/course-form.html.twig',
                [
                    'pageTitle' => 'Edit Course',
                    'isEdit' => true,
                    'course' => $course,
                ]
            )
            ->willReturn($this->response);

        // Call the editCourseForm method
        $result = $this->adminController->editCourseForm($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testEditCourseFormMethodWithInvalidCourseIdRedirects(): void
    {
        $courseId = 999;

        // Mock the repository to return null (course not found)
        $this->courseRepository->expects($this->once())
            ->method('findById')
            ->with($courseId)
            ->willReturn(null);

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin/courses')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the editCourseForm method
        $result = $this->adminController->editCourseForm($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testEditCourseMethodWithValidDataUpdatesCourseAndRedirects(): void
    {
        $courseId = 1;
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');
        $course = new Course(
            $courseId,
            'PHP Course',
            'Learn PHP programming',
            false,
            false,
            $createdAt
        );

        // Mock the repository to return a course
        $this->courseRepository->expects($this->once())
            ->method('findById')
            ->with($courseId)
            ->willReturn($course);

        // Mock the request to return parsed body with valid data
        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'title' => 'Updated PHP Course',
                'description' => 'Updated description',
                'is_promoted' => '1'
            ]);

        // Mock the repository save method to return the updated course
        $this->courseRepository->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($course) {
                // Return the same course to simulate the database update
                return $course;
            });

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin/courses')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the editCourse method
        $result = $this->adminController->editCourse($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testEditCourseMethodWithInvalidCourseIdRedirects(): void
    {
        $courseId = 999;

        // Mock the repository to return null (course not found)
        $this->courseRepository->expects($this->once())
            ->method('findById')
            ->with($courseId)
            ->willReturn(null);

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin/courses')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the editCourse method
        $result = $this->adminController->editCourse($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testEditCourseMethodWithInvalidDataRedirectsToEditForm(): void
    {
        $courseId = 1;
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');
        $course = new Course(
            $courseId,
            'PHP Course',
            'Learn PHP programming',
            false,
            false,
            $createdAt
        );

        // Mock the repository to return a course
        $this->courseRepository->expects($this->once())
            ->method('findById')
            ->with($courseId)
            ->willReturn($course);

        // Mock the request to return parsed body with invalid data (empty title)
        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'title' => '',
                'description' => 'Updated description',
                'is_promoted' => '1'
            ]);

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin/courses/edit/' . $courseId)
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the editCourse method
        $result = $this->adminController->editCourse($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testArchiveCourseMethodCallsRepositoryAndRedirects(): void
    {
        $courseId = 1;

        // Mock the repository archive method
        $this->courseRepository->expects($this->once())
            ->method('archive')
            ->with($courseId);

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/admin/courses')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the archiveCourse method
        $result = $this->adminController->archiveCourse($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }
}
