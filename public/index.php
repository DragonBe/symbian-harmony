<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Set up dependency injection container
$containerBuilder = new ContainerBuilder();

// Add DI definitions
$definitions = require __DIR__ . '/../config/di-definitions.php';
$containerBuilder->addDefinitions($definitions);

// Build container
$container = $containerBuilder->build();

// Create app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Add Twig middleware
$app->add(TwigMiddleware::createFromContainer($app));

// Define routes
$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

// Run app
$app->run();