<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CourseRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CourseController
{
    private Twig $twig;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(Twig $twig, CourseRepositoryInterface $courseRepository)
    {
        $this->twig = $twig;
        $this->courseRepository = $courseRepository;
    }

    public function listCourses(Request $request, Response $response): Response
    {
        $courses = $this->courseRepository->findAll();
        
        return $this->twig->render($response, 'courses.html.twig', [
            'pageTitle' => 'All Training Courses',
            'courses' => $courses,
        ]);
    }
    
    public function searchForm(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'search.html.twig', [
            'pageTitle' => 'Search Training Courses',
        ]);
    }
    
    public function search(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $searchTerm = $params['q'] ?? '';
        
        $courses = [];
        if (!empty($searchTerm)) {
            $courses = $this->courseRepository->findBySearchTerm($searchTerm);
        }
        
        return $this->twig->render($response, 'search.html.twig', [
            'pageTitle' => 'Search Training Courses',
            'searchTerm' => $searchTerm,
            'courses' => $courses,
        ]);
    }
    
    public function registrationForm(Request $request, Response $response, array $args): Response
    {
        $courseId = (int) $args['id'];
        $course = $this->courseRepository->findById($courseId);
        
        if (!$course) {
            // Course not found, redirect to courses list
            return $response->withHeader('Location', '/courses')->withStatus(302);
        }
        
        return $this->twig->render($response, 'register.html.twig', [
            'pageTitle' => 'Register for Course',
            'course' => $course,
        ]);
    }
    
    public function register(Request $request, Response $response, array $args): Response
    {
        $courseId = (int) $args['id'];
        $course = $this->courseRepository->findById($courseId);
        
        if (!$course) {
            // Course not found, redirect to courses list
            return $response->withHeader('Location', '/courses')->withStatus(302);
        }
        
        $data = $request->getParsedBody();
        
        // In a real application, we would validate the data and save the registration
        // For now, we'll just redirect to the confirmation page
        
        return $response->withHeader('Location', '/register/confirmation')->withStatus(302);
    }
    
    public function confirmation(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'confirmation.html.twig', [
            'pageTitle' => 'Registration Confirmation',
        ]);
    }
}