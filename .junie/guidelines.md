# Junie Guidelines for Slim Framework PHP Development

This file contains guidelines for Junie to follow when working on this Slim Framework PHP project. Adhering to these standards ensures consistence

## Core technology and versions

- **PHP:** Use the latest version of PHP, version 8.4.8, unless otherwise specified
- **Slim Framework:** Use the latest version of Slim Framework for structuring the project, version 4.14.0, unless otherwise specified
- **Database:** Use MySQL Community Server 8.4.5 LTS for storing data
- **Bootstrap:** Use Boostrap version 5.3.6. for visualisation of components of the website
- **Twig:** Use the latest version of Twig view, version 3.4.1, for Slim Framework to create HTML templates

## Project structure

This project adheres to the recommended directory structure for Slim Framework projects as provided by the Slim-Skeleton project on GitHub <https://github.com/slimphp/Slim-Skeleton>, with the addition of a `templates` directory in the root of the project for Twig templates, and a `config` directory for dependency definitions.

```text
/
|- config/
|  |- di-definitions.php                              # Dependency definitions
|- public/
|  |- assets/
|  |  |- css/
|  |  |  |- app.css                                   # Custom CSS styling
|  |  |- media/
|  |- index.php                                       # The application entrypoint
|- src/
|  |- controller/
|  |- handler/
|  |- model/
|  |- repository/
|  |- service/
|- templates/
|  |- base.html.twig                                  # The base HTML template
|- tests/
|  |- acceptance/
|  |- unit/
|  |  |- controller/
|  |  |- handler/
|  |  |- model/
|  |  |- repository/
|  |  |- service/
|- vendor/
```

## Data access

- **PDO:** We use PDO with the MySQL driver to connect with the database
- **Repository:** We use repositories to abstract how we read and write data to the database

## Data migrations

- **Phinx:** We use Phinx to manage database migrations

## PHP language features

- **Strict Types:** We use strict types enabled to enforce correct type usage
- **Type hinting:** We apply type hinting everywhere
- **Namespaces:** We use correct namespaces

## Best practices

- **Immutable models:** We use immutable models
- **Dependency injection:** We use PHP-DI for managing dependency injection
- **PSR:** 
    - **PSR4:** for autoloading
    - **PSR7:** for http messaging
    - **PSR12:** for extended coding standards

## Logging

- **Monolog:** use the latest version of Monolog for logging purposes

## Testing

- **Unit testing:** Use the latest version of PHPUnit for unit testing
- **Acceptance testing:** Use the latest version of Behat for acceptance testing

## Code quality

- **PHP Code Sniffer:** Make sure the code adheres to PSR-12 extended coding standards
- **PHPStan:** Make sure that the code passes a minimum of level 6 of standards defined by PHPStan
- **Coverage:** Make sure that the code coverage does not fall below 85%
