<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Finance Tracker App

A simple financial tracking application built with Laravel and Filament Admin Panel. This app helps you manage income, expenses, and overall financial insights.

## Getting Started

These instructions will help you set up the project locally for development and testing.

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL or any database supported by Laravel
- A web server (e.g., XAMPP, WAMP, Laravel Valet)

---

## Installation

1. **Clone the Repository**

   Clone the repository from GitHub:
   ```bash
   git clone https://github.com/username/finance-tracker-app.git


2. Navigate into the project directory:

    ```bash
    cd project-name

3. Install Composer dependencies:

   ```bash
   composer install
   
4. Copy .env.example to .env and configure your environment variables, especially the database connection settings:

   ```bash
   cp .env.example .env

5. Generate the application key:

   ```bash
   php artisan key:generate

6. Run the database migrations (Ensure your database connection is configured in .env):

   ```bash
   php artisan migrate

7. Install npm dependencies and compile assets:

   ```bash
   npm install && npm run dev

### Running Project

```bash
php artisan serve
```
### Login Information!

Find the email & password login in database & seeders folder.
