# Training Courses Web Application

A web application where users can look at promoted training courses, search for specific training courses, and register for a training course. Site owners can sign in to the backend to add, change, or archive training courses.

## Features

- View promoted training courses on the home page
- Browse all available training courses
- Search for specific training courses
- Register for a training course
- Admin backend for managing courses

## Technologies Used

- PHP 8.4.8
- Slim Framework 4.14.0
- MySQL 8.4.5
- Bootstrap 5.3.6
- Twig 3.4.1

## Setup Instructions

1. Clone the repository
2. Install dependencies with Composer:
   ```
   composer install
   ```
3. Create a MySQL database and update the `.env` file with your database credentials
4. Run the database migrations:
   ```
   vendor/bin/phinx migrate
   ```
5. Seed the database with initial courses:
   ```
   vendor/bin/phinx seed:run
   ```
6. Start the PHP development server:
   ```
   composer start
   ```
7. Visit `http://localhost:8080` in your browser

## Admin Access

To access the admin area, go to `http://localhost:8080/admin/login` and use the following credentials:
- Username: admin
- Password: password

## Development Guidelines

See the [guidelines](.junie/guidelines.md) for development standards and practices.
