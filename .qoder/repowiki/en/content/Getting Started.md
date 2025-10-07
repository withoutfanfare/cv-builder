# Getting Started

<cite>
**Referenced Files in This Document**   
- [README.md](file://README.md)
- [composer.json](file://composer.json)
- [package.json](file://package.json)
- [vite.config.js](file://vite.config.js)
- [.env.example](file://.env.example)
- [DatabaseSeeder.php](file://database/seeders/DatabaseSeeder.php)
- [PdfTemplateSeeder.php](file://database/seeders/PdfTemplateSeeder.php)
- [services.php](file://config/services.php)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Prerequisites](#prerequisites)
3. [Cloning the Repository](#cloning-the-repository)
4. [Installing PHP Dependencies](#installing-php-dependencies)
5. [Installing Node.js Dependencies](#installing-nodejs-dependencies)
6. [Environment Configuration](#environment-configuration)
7. [Database Setup and Migrations](#database-setup-and-migrations)
8. [OpenAI API Configuration](#openai-api-configuration)
9. [Running Database Seeders](#running-database-seeders)
10. [Starting the Development Server](#starting-the-development-server)
11. [Verification Steps](#verification-steps)
12. [Common Setup Issues and Solutions](#common-setup-issues-and-solutions)

## Introduction
This guide provides a comprehensive walkthrough for setting up the cv-builder application locally. It covers all essential steps from repository cloning to launching the development server, with detailed instructions for dependency installation, environment configuration, database setup, and service integration. The guide is designed to be beginner-friendly while offering technical depth for troubleshooting common issues.

## Prerequisites
Before beginning the setup process, ensure your development environment meets the following requirements:
- **PHP 8.2 or higher** installed and available in your system PATH
- **Composer** for PHP dependency management
- **Node.js 18+** with **npm** or **yarn**
- A database system (MySQL 5.7+/8.0 or SQLite)
- Git for version control

Verify your installations with:
```bash
php -v
composer --version
node -v
npm -v
```

**Section sources**
- [README.md](file://README.md#L0-L215)

## Cloning the Repository
Begin by cloning the cv-builder repository from its source:

```bash
git clone https://github.com/withoutfanfare/cv-builder.git
cd cv-builder
```

This creates a local copy of the project with the complete directory structure, including all source files, configuration, and assets needed for development.

**Section sources**
- [README.md](file://README.md#L25-L28)

## Installing PHP Dependencies
Install all required PHP packages using Composer:

```bash
composer install
```

This command reads the `composer.json` file and installs all dependencies specified in the `require` and `require-dev` sections, including Laravel 12, Filament 4, OpenAI client, and Spatie Laravel-PDF. The installation includes essential packages for framework functionality, testing, and development tools.

**Section sources**
- [composer.json](file://composer.json#L0-L83)
- [README.md](file://README.md#L30-L31)

## Installing Node.js Dependencies
Install frontend dependencies using npm:

```bash
npm install
```

This installs development tools and libraries specified in `package.json`, including Vite for asset building, Tailwind CSS for styling, Axios for HTTP requests, and Puppeteer for PDF generation. The `devDependencies` include Laravel Vite Plugin and concurrently for development server management.

**Section sources**
- [package.json](file://package.json#L0-L20)
- [README.md](file://README.md#L30-L31)

## Environment Configuration
Configure the application environment by creating and editing the `.env` file:

```bash
cp .env.example .env
```

Key environment variables to configure:
- **APP_NAME**: Your application name
- **APP_ENV**: Set to `local` for development
- **APP_KEY**: Generate with `php artisan key:generate`
- **DB_CONNECTION**: Choose `mysql` or `sqlite`
- **OPENAI_API_KEY**: Your OpenAI API key

For SQLite, create the database file:
```bash
touch database/database.sqlite
```

**Section sources**
- [README.md](file://README.md#L33-L37)

## Database Setup and Migrations
Run database migrations to create all necessary tables:

```bash
php artisan migrate
```

This executes all migration files in `database/migrations/`, creating tables for users, CVs, job applications, cover letters, PDF snapshots, and related entities. The migration process establishes the complete database schema as defined in the application's data model.

**Section sources**
- [README.md](file://README.md#L39-L40)

## OpenAI API Configuration
Configure OpenAI integration by setting the API key in your `.env` file:

```env
OPENAI_API_KEY=your_api_key_here
OPENAI_MODEL=gpt-4-turbo-preview
OPENAI_MONTHLY_BUDGET_CENTS=5000
```

The OpenAI configuration is managed in `config/services.php`, where the API key, model selection, and monthly budget are defined. This integration enables AI-powered features such as CV review, keyword analysis, and content suggestions.

**Section sources**
- [config/services.php](file://config/services.php#L0-L44)
- [README.md](file://README.md#L33-L37)

## Running Database Seeders
Seed the database with initial data:

```bash
php artisan db:seed
```

This executes the `DatabaseSeeder` which creates a test user and calls `BaseCVSeeder`. Additionally, you can run specific seeders:
```bash
php artisan db:seed --class=PdfTemplateSeeder
```

The `PdfTemplateSeeder` populates the database with three default templates:
- **Default**: Classic CV template with traditional layout
- **Modern**: Clean design with color accents
- **Classic**: Traditional template with serif fonts

These templates are stored in the `pdf_templates` table and can be used for CV PDF generation.

**Section sources**
- [DatabaseSeeder.php](file://database/seeders/DatabaseSeeder.php#L0-L27)
- [PdfTemplateSeeder.php](file://database/seeders/PdfTemplateSeeder.php#L0-L46)
- [README.md](file://README.md#L40-L41)

## Starting the Development Server
Launch the application using Artisan and Vite:

```bash
# For development with hot reload
npm run dev
php artisan serve
```

Or use the convenience script to run all services simultaneously:
```bash
composer dev
```

This starts the PHP development server, queue listener, log watcher, and Vite development server concurrently, enabling real-time asset compilation and immediate feedback during development.

**Section sources**
- [vite.config.js](file://vite.config.js#L0-L13)
- [README.md](file://README.md#L43-L44)
- [composer.json](file://composer.json#L65-L75)

## Verification Steps
Confirm successful installation by completing these verification steps:

1. **Access the application**: Navigate to `http://localhost:8000/admin` in your browser
2. **Login**: Use the seeded credentials:
   - Email: `test@example.com`
   - Password: `password`
3. **Create a test CV**: 
   - Go to **CVs** → **Create**
   - Fill in personal information and add experience/education
   - Save the CV
4. **Generate PDF**: 
   - View the created CV
   - Select a template and generate PDF
5. **Check OpenAI integration**: 
   - Use AI review features if available
   - Verify no API errors in browser console

Successful completion of these steps confirms a properly configured local environment.

**Section sources**
- [README.md](file://README.md#L46-L47)

## Common Setup Issues and Solutions
Address frequent setup problems with these troubleshooting solutions:

**Database Connection Errors**
- **Issue**: "SQLSTATE[HY000] [2002] Connection refused"
- **Solution**: Verify database service is running and credentials in `.env` are correct

**Missing PHP Extensions**
- **Issue**: "The each() function is deprecated"
- **Solution**: Ensure PHP 8.2+ is used and required extensions (PDO, OpenSSL, Mbstring) are enabled

**Vite Asset Compilation Failures**
- **Issue**: "Cannot find module 'vite'"
- **Solution**: Run `npm install` again and verify Node.js version compatibility

**OpenAI Integration Issues**
- **Issue**: "Invalid API key"
- **Solution**: Verify `OPENAI_API_KEY` is correctly set in `.env` and has sufficient quota

**Migration Errors**
- **Issue**: "Unknown database"
- **Solution**: For MySQL, create the database manually: `CREATE DATABASE cv_builder;`

**File Permission Issues**
- **Issue**: "Failed to open stream: Permission denied"
- **Solution**: Set proper permissions: `chmod -R 775 storage bootstrap/cache`

These solutions address the most common obstacles encountered during local setup, ensuring a smooth development experience.

**Section sources**
- [README.md](file://README.md#L39-L40)
- [config/services.php](file://config/services.php#L0-L44)
- [composer.json](file://composer.json#L0-L83)