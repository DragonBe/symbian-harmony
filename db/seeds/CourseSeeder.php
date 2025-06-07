<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CourseSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'title' => 'PHP for beginners',
                'description' => 'Learn the basics of PHP programming language. This course covers variables, control structures, functions, and more.',
                'is_promoted' => false,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'title' => 'Advanced PHP',
                'description' => 'Take your PHP skills to the next level. This course covers object-oriented programming, design patterns, and advanced PHP features.',
                'is_promoted' => false,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'title' => 'Unit testing with PHPUnit',
                'description' => 'Learn how to write effective unit tests for your PHP code using PHPUnit. This course covers test-driven development, mocking, and more.',
                'is_promoted' => true,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'title' => 'Getting started with Slim Framework',
                'description' => 'Build modern web applications with Slim Framework. This course covers routing, middleware, dependency injection, and more.',
                'is_promoted' => true,
                'is_archived' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
        ];

        $courses = $this->table('courses');
        $courses->insert($data)->save();
    }
}