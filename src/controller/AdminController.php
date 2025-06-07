<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Course;
use App\Repository\CourseRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AdminController
{
    private Twig $twig;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(Twig $twig, CourseRepositoryInterface $courseRepository)
    {
        $this->twig = $twig;
        $this->courseRepository = $courseRepository;
    }

    public function dashboard(Request $request, Response $response): Response
    {
        // In a real application, we would check if the user is authenticated
        
        return $this->twig->render($response, 'admin/dashboard.html.twig', [
            'pageTitle' => 'Admin Dashboard',
        ]);
    }
    
    public function loginForm(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'admin/login.html.twig', [
            'pageTitle' => 'Admin Login',
        ]);
    }
    
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        // In a real application, we would validate the credentials
        // For now, we'll just redirect to the admin dashboard
        
        return $response->withHeader('Location', '/admin')->withStatus(302);
    }
    
    public function listCourses(Request $request, Response $response): Response
    {
        // In a real application, we would check if the user is authenticated
        
        $courses = $this->courseRepository->findAll();
        
        return $this->twig->render($response, 'admin/courses.html.twig', [
            'pageTitle' => 'Manage Courses',
            'courses' => $courses,
        ]);
    }
    
    public function addCourseForm(Request $request, Response $response): Response
    {
        // In a real application, we would check if the user is authenticated
        
        return $this->twig->render($response, 'admin/course-form.html.twig', [
            'pageTitle' => 'Add Course',
            'isEdit' => false,
        ]);
    }
    
    public function addCourse(Request $request, Response $response): Response
    {
        // In a real application, we would check if the user is authenticated
        
        $data = $request->getParsedBody();
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $isPromoted = isset($data['is_promoted']) && $data['is_promoted'] === '1';
        
        // Validate data
        if (empty($title) || empty($description)) {
            // In a real application, we would show validation errors
            return $response->withHeader('Location', '/admin/courses/add')->withStatus(302);
        }
        
        // Create and save the course
        $course = Course::create($title, $description, $isPromoted);
        $this->courseRepository->save($course);
        
        return $response->withHeader('Location', '/admin/courses')->withStatus(302);
    }
    
    public function editCourseForm(Request $request, Response $response, array $args): Response
    {
        // In a real application, we would check if the user is authenticated
        
        $courseId = (int) $args['id'];
        $course = $this->courseRepository->findById($courseId);
        
        if (!$course) {
            // Course not found, redirect to courses list
            return $response->withHeader('Location', '/admin/courses')->withStatus(302);
        }
        
        return $this->twig->render($response, 'admin/course-form.html.twig', [
            'pageTitle' => 'Edit Course',
            'isEdit' => true,
            'course' => $course,
        ]);
    }
    
    public function editCourse(Request $request, Response $response, array $args): Response
    {
        // In a real application, we would check if the user is authenticated
        
        $courseId = (int) $args['id'];
        $course = $this->courseRepository->findById($courseId);
        
        if (!$course) {
            // Course not found, redirect to courses list
            return $response->withHeader('Location', '/admin/courses')->withStatus(302);
        }
        
        $data = $request->getParsedBody();
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $isPromoted = isset($data['is_promoted']) && $data['is_promoted'] === '1';
        
        // Validate data
        if (empty($title) || empty($description)) {
            // In a real application, we would show validation errors
            return $response->withHeader('Location', '/admin/courses/edit/' . $courseId)->withStatus(302);
        }
        
        // Update and save the course
        $updatedCourse = $course
            ->withTitle($title)
            ->withDescription($description)
            ->withPromoted($isPromoted);
        
        $this->courseRepository->save($updatedCourse);
        
        return $response->withHeader('Location', '/admin/courses')->withStatus(302);
    }
    
    public function archiveCourse(Request $request, Response $response, array $args): Response
    {
        // In a real application, we would check if the user is authenticated
        
        $courseId = (int) $args['id'];
        $this->courseRepository->archive($courseId);
        
        return $response->withHeader('Location', '/admin/courses')->withStatus(302);
    }
}