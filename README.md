# CV Builder

A focused job application productivity tool built with Laravel 12 and Filament 4. Track applications, tailor CVs, manage cover letters, and optimize your job search workflow - all in one place.

## 🎯 Overview

CV Builder helps you manage the entire job application lifecycle:
- **Create and version multiple CVs** with structured sections (experience, projects, education, skills)
- **Track job applications** with statuses, deadlines, and next actions
- **Generate tailored PDFs** with custom section profiles
- **Build cover letters** from templates with variable interpolation
- **Monitor application metrics** and interview pipelines
- **Link skill evidence** to specific experiences for better alignment

Built following **KISS** and **YAGNI** principles - only essential features, implemented when validated as necessary.

---

## 🚀 Quick Start

Get up and running in 5 minutes:

```bash
# Clone the repository
git clone https://github.com/withoutfanfare/cv-builder.git
cd cv-builder

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Set up database (update .env with your MySQL credentials)
# Or use SQLite: touch database/database.sqlite
php artisan migrate
php artisan db:seed  # Optional: loads demo data

# Build assets and start server
npm run build
php artisan serve
```

**Access the admin panel**: Navigate to `http://localhost:8000/admin`

For detailed setup instructions, troubleshooting, and advanced configuration, see the [comprehensive documentation](.qoder/repowiki/en/content/Getting%20Started.md).

---

## 📚 Documentation

Complete documentation is available in the [RepoWiki](.qoder/repowiki/en/content/):

- **[Getting Started](.qoder/repowiki/en/content/Getting%20Started.md)** - Installation, configuration, and troubleshooting
- **[Core Concepts](.qoder/repowiki/en/content/Core%20Concepts/)** - Understanding the application structure
- **[CV Management](.qoder/repowiki/en/content/CV%20Management/)** - Creating and managing CVs
- **[Job Application Tracking](.qoder/repowiki/en/content/Job%20Application%20Tracking/)** - Application lifecycle management
- **[PDF Generation](.qoder/repowiki/en/content/PDF%20Generation/)** - Customizing and generating PDF outputs
- **[Data Model](.qoder/repowiki/en/content/Data%20Model/)** - Database schema and relationships
- **[Architecture & Design](.qoder/repowiki/en/content/Architecture%20&%20Design/)** - System design principles
- **[Extending the Application](.qoder/repowiki/en/content/Extending%20the%20Application/)** - Adding custom features
- **[Testing Strategy](.qoder/repowiki/en/content/Testing%20Strategy.md)** - Test-driven development approach

---

## ✨ Features

### Phase 1: Operational Clarity ✅
**Goal**: Daily action planning and job spec capture

- Extended job application fields with auto-activity tracking
- Needs Attention dashboard widget
- Keyword coverage analysis
- PDF snapshots with hash verification
- CV cloning and versioning

### Phase 2: Insight & Iteration ✅
**Goal**: Performance metrics and interview preparation

- Application events timeline
- Metrics dashboard (velocity, response rates, conversion rates)
- Interview preparation with structured metadata
- Post-interview reflections
- Achievement bullet refiner

### Phase 3: Tailoring Efficiency ✅
**Goal**: Faster CV customization and alignment

- Section focus profiles (non-destructive reordering)
- Enhanced keyword scoring with weighted prominence
- Cover letter builder with template interpolation
- Skill evidence linking
- Cover letter versioning with multiple tones

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Admin UI**: Filament 4
- **Database**: MySQL/SQLite
- **Testing**: Pest PHP 3.8
- **Frontend**: Vite + Tailwind CSS 4
- **PDF Generation**: Spatie Laravel-PDF
- **Code Style**: Laravel Pint

---

## 💻 Development

### Common Commands

```bash
# Run all services (server, queue, logs, Vite)
composer dev

# Run tests
composer test

# Format code
./vendor/bin/pint

# Database operations
php artisan migrate
php artisan migrate:fresh --seed
```

For complete development workflow, configuration details, and troubleshooting, see [Getting Started](.qoder/repowiki/en/content/Getting%20Started.md).

---

## 🧪 Testing

Built with Test-Driven Development (TDD). All features have comprehensive test coverage:

```bash
composer test              # Run all tests
php artisan test --filter  # Run specific tests
./vendor/bin/pest          # Run with Pest directly
```

See [Testing Strategy](.qoder/repowiki/en/content/Testing%20Strategy.md) for methodology and best practices.

---

## 📖 Usage Examples

### Creating Your First CV
Navigate to **CVs** → **Create** and add sections (experience, projects, education, skills) via relation managers.

### Tracking a Job Application
Create an application under **Job Applications**, link your CV, add job details, and track status through the pipeline: `draft` → `pending` → `interviewing` → `offer/rejected`.

### Tailoring with Section Profiles
Create custom profiles (e.g., "Frontend Focus") to reorder and filter CV sections non-destructively for different roles.

### Building Cover Letters
Use the template system with variables like `{{company_name}}` and `{{role_title}}` that auto-populate from your job application.

For detailed walkthroughs, see the [CV Management](.qoder/repowiki/en/content/CV%20Management/) and [Job Application Tracking](.qoder/repowiki/en/content/Job%20Application%20Tracking/) documentation.

---

## 📐 Architecture

Standard Laravel MVC architecture with Filament admin panel. Key components:

- **Models**: `Cv`, `JobApplication`, `ApplicationEvent`, `CoverLetter`, `SectionFocusProfile`, `PdfSnapshot`
- **Services**: `KeywordScoringService`, `CoverLetterService`, `PdfService`, `MetricsCalculationService`
- **Filament Resources**: CRUD operations with relation managers for nested data

See [Architecture & Design](.qoder/repowiki/en/content/Architecture%20&%20Design/) for detailed system design.

---

## 🗺️ Project Status

**Current Implementation**: Phases 1-3 Complete ✅
- ✅ Phase 1: Operational Clarity
- ✅ Phase 2: Insight & Iteration  
- ✅ Phase 3: Tailoring Efficiency
- ⚠️ Phase 4: Advanced Optimization (validation required)

Phase 4 features (AI assistance, A/B testing, full-text search) follow YAGNI principles and require real-world validation before implementation.

---

## 🤝 Contributing

Built with simplicity and pragmatism in mind:
- **KISS**: Keep it simple
- **YAGNI**: Build only what's validated as necessary
- **TDD**: Tests first, always
- **Laravel Conventions**: Follow framework patterns

---

## 📄 License

MIT License - see LICENSE file for details.

---

## 🔗 Additional Resources

- **[Complete Documentation](.qoder/repowiki/en/content/)** - Full RepoWiki documentation
- **[Technology Stack](.qoder/repowiki/en/content/Technology%20Stack.md)** - Detailed tech stack overview
- **[Deployment Guide](.qoder/repowiki/en/content/Deployment%20&%20Maintenance.md)** - Production deployment
- **[Extending the App](.qoder/repowiki/en/content/Extending%20the%20Application/)** - Custom feature development

---

**Built with Laravel 12 + Filament 4 | Following KISS & YAGNI principles**
