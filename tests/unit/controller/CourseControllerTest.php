<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\CourseController;
use App\Model\Course;
use App\Repository\CourseRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

/**
 * @coversClass \App\Controller\CourseController
 */
class CourseControllerTest extends TestCase
{
    private $twig;
    private $courseRepository;
    private $request;
    private $response;
    private $courseController;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Twig::class);
        $this->courseRepository = $this->createMock(CourseRepositoryInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->courseController = new CourseController($this->twig, $this->courseRepository);
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
                'courses.html.twig',
                [
                    'pageTitle' => 'All Training Courses',
                    'courses' => $courses,
                ]
            )
            ->willReturn($this->response);

        // Call the listCourses method
        $result = $this->courseController->listCourses($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testSearchFormMethodRendersTemplate(): void
    {
        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'search.html.twig',
                [
                    'pageTitle' => 'Search Training Courses',
                ]
            )
            ->willReturn($this->response);

        // Call the searchForm method
        $result = $this->courseController->searchForm($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testSearchMethodWithEmptyQueryDoesNotCallRepository(): void
    {
        // Mock the request to return empty query params
        $this->request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        // Repository should not be called
        $this->courseRepository->expects($this->never())
            ->method('findBySearchTerm');

        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'search.html.twig',
                [
                    'pageTitle' => 'Search Training Courses',
                    'searchTerm' => '',
                    'courses' => [],
                ]
            )
            ->willReturn($this->response);

        // Call the search method
        $result = $this->courseController->search($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testSearchMethodWithQueryCallsRepository(): void
    {
        // Mock the request to return query params with a search term
        $this->request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['q' => 'php']);

        // Mock the repository to return some courses
        $courses = ['course1', 'course2'];
        $this->courseRepository->expects($this->once())
            ->method('findBySearchTerm')
            ->with('php')
            ->willReturn($courses);

        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'search.html.twig',
                [
                    'pageTitle' => 'Search Training Courses',
                    'searchTerm' => 'php',
                    'courses' => $courses,
                ]
            )
            ->willReturn($this->response);

        // Call the search method
        $result = $this->courseController->search($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testRegistrationFormMethodWithValidCourseId(): void
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
                'register.html.twig',
                [
                    'pageTitle' => 'Register for Course',
                    'course' => $course,
                ]
            )
            ->willReturn($this->response);

        // Call the registrationForm method
        $result = $this->courseController->registrationForm($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }

    public function testRegistrationFormMethodWithInvalidCourseIdRedirects(): void
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
            ->with('Location', '/courses')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the registrationForm method
        $result = $this->courseController->registrationForm($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testRegisterMethodWithValidCourseId(): void
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

        // Mock the request to return parsed body
        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'John Doe', 'email' => 'john@example.com']);

        // Mock the response withHeader and withStatus methods
        $redirectResponse = $this->createMock(ResponseInterface::class);
        $this->response->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/register/confirmation')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the register method
        $result = $this->courseController->register($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testRegisterMethodWithInvalidCourseIdRedirects(): void
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
            ->with('Location', '/courses')
            ->willReturn($redirectResponse);
        $redirectResponse->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturn($redirectResponse);

        // Call the register method
        $result = $this->courseController->register($this->request, $this->response, ['id' => (string)$courseId]);

        // Assert that the result is the redirect response
        $this->assertSame($redirectResponse, $result);
    }

    public function testConfirmationMethodRendersTemplate(): void
    {
        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'confirmation.html.twig',
                [
                    'pageTitle' => 'Registration Confirmation',
                ]
            )
            ->willReturn($this->response);

        // Call the confirmation method
        $result = $this->courseController->confirmation($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }
}
