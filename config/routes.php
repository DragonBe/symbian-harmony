<?php
declare(strict_types=1);

use App\Controller\AdminController;
use App\Controller\CourseController;
use App\Controller\HomeController;
use Slim\App;

return function (App $app) {
    // Home page - shows promoted courses
    $app->get('/', [HomeController::class, 'home']);

    // All courses page
    $app->get('/courses', [CourseController::class, 'listCourses']);

    // Search courses
    $app->get('/search', [CourseController::class, 'searchForm']);
    $app->get('/search/results', [CourseController::class, 'search']);

    // Course registration
    $app->get('/register/{id}', [CourseController::class, 'registrationForm']);
    $app->post('/register/{id}', [CourseController::class, 'register']);
    $app->get('/register/confirmation', [CourseController::class, 'confirmation']);

    // Admin routes
    $app->get('/admin', [AdminController::class, 'dashboard']);
    $app->get('/admin/login', [AdminController::class, 'loginForm']);
    $app->post('/admin/login', [AdminController::class, 'login']);

    $app->get('/admin/courses', [AdminController::class, 'listCourses']);
    $app->get('/admin/courses/add', [AdminController::class, 'addCourseForm']);
    $app->post('/admin/courses/add', [AdminController::class, 'addCourse']);
    $app->get('/admin/courses/edit/{id}', [AdminController::class, 'editCourseForm']);
    $app->post('/admin/courses/edit/{id}', [AdminController::class, 'editCourse']);
    $app->post('/admin/courses/archive/{id}', [AdminController::class, 'archiveCourse']);
};
