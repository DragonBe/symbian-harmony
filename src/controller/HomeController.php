<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CourseRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    private Twig $twig;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(Twig $twig, CourseRepositoryInterface $courseRepository)
    {
        $this->twig = $twig;
        $this->courseRepository = $courseRepository;
    }

    public function home(Request $request, Response $response): Response
    {
        $promotedCourses = $this->courseRepository->findPromoted();
        
        return $this->twig->render($response, 'home.html.twig', [
            'pageTitle' => 'Training Courses - Home',
            'promotedCourses' => $promotedCourses,
        ]);
    }
}