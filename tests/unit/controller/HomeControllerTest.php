<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\HomeController;
use App\Repository\CourseRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

/**
 * @coversClass \App\Controller\HomeController
 */
class HomeControllerTest extends TestCase
{
    private $twig;
    private $courseRepository;
    private $request;
    private $response;
    private $homeController;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Twig::class);
        $this->courseRepository = $this->createMock(CourseRepositoryInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->homeController = new HomeController($this->twig, $this->courseRepository);
    }

    public function testHomeMethodCallsRepositoryAndRendersTemplate(): void
    {
        // Mock the repository to return some promoted courses
        $promotedCourses = ['course1', 'course2'];
        $this->courseRepository->expects($this->once())
            ->method('findPromoted')
            ->willReturn($promotedCourses);

        // Mock the Twig render method to return the response
        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'home.html.twig',
                [
                    'pageTitle' => 'Training Courses - Home',
                    'promotedCourses' => $promotedCourses,
                ]
            )
            ->willReturn($this->response);

        // Call the home method
        $result = $this->homeController->home($this->request, $this->response);

        // Assert that the result is the response
        $this->assertSame($this->response, $result);
    }
}
