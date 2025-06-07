<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;

return [
    // Twig view renderer
    Twig::class => function (ContainerInterface $c) {
        $twig = Twig::create(__DIR__ . '/../templates', [
            'cache' => false,
            'debug' => true,
            'auto_reload' => true,
        ]);

        // Add extensions if needed
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        return $twig;
    },

    // Monolog
    Logger::class => function (ContainerInterface $c) {
        $logger = new Logger('app');

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG);
        $logger->pushHandler($handler);

        return $logger;
    },

    // Database PDO connection
    PDO::class => function (ContainerInterface $c) {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? 'training_courses';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $username, $password, $options);
    },

    // Repositories
    App\Repository\CourseRepositoryInterface::class => function (ContainerInterface $c) {
        return new App\Repository\CourseRepository($c->get(PDO::class));
    },

    // Controllers
    App\Controller\HomeController::class => function (ContainerInterface $c) {
        return new App\Controller\HomeController(
            $c->get(Twig::class),
            $c->get(App\Repository\CourseRepositoryInterface::class)
        );
    },

    App\Controller\CourseController::class => function (ContainerInterface $c) {
        return new App\Controller\CourseController(
            $c->get(Twig::class),
            $c->get(App\Repository\CourseRepositoryInterface::class)
        );
    },

    App\Controller\AdminController::class => function (ContainerInterface $c) {
        return new App\Controller\AdminController(
            $c->get(Twig::class),
            $c->get(App\Repository\CourseRepositoryInterface::class)
        );
    },
];
