<?php

namespace Database\Seeders;

use App\Models\Cv;
use App\Models\CvCustomSection;
use App\Models\CvEducation;
use App\Models\CvExperience;
use App\Models\CvHeaderInfo;
use App\Models\CvProject;
use App\Models\CvReference;
use App\Models\CvSection;
use App\Models\CvSkillCategory;
use App\Models\CvSummary;
use App\Models\JobApplication;
use Illuminate\Database\Seeder;

class BaseCVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create CV
        $cv = Cv::create([
            'title' => 'Alex Thompson - Senior Full-Stack Developer',
        ]);

        // Create Header Info
        CvHeaderInfo::create([
            'cv_id' => $cv->id,
            'full_name' => 'Alex Thompson',
            'job_title' => 'Senior Full-Stack Developer',
            'phone' => '+44 7700 900456',
            'email' => 'alex.thompson@example.com',
            'location' => 'Leeds, West Yorkshire',
            'linkedin_url' => 'https://linkedin.com/in/alexthompson',
            'github_url' => 'https://github.com/alexthompson',
            'website_url' => 'https://alexthompson.dev',
        ]);

        // Summary Section
        $summarySection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'summary',
            'display_order' => 1,
        ]);

        CvSummary::create([
            'cv_section_id' => $summarySection->id,
            'content' => "Senior Laravel developer with over 20 years' PHP and full-stack experience, and a record of shipping quality products. Founded and scaled a successful web agency (serving over 20+ clients), now heavily leaning into AI-driven systems and integration.\n\nHands-on with DevOps and cloud automation; a trusted mentor who turns complex goals into elegant, scalable solutions.\n\nMy work blends solid technical expertise with a passion for optimising performance, crafting elegant systems, boosting revenue, and increasing customer satisfaction. I'm looking to bring my technical expertise, design skills, and project leadership to a dynamic team, focusing on cutting-edge web development and innovative problem-solving.",
        ]);

        // Custom Section Example
        $customSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'custom',
            'title' => 'Key Achievements',
            'display_order' => 2,
        ]);

        CvCustomSection::create([
            'cv_section_id' => $customSection->id,
            'content' => "• Successfully migrated 20+ client websites to modern cloud infrastructure with zero downtime\n• Reduced average page load times by 60% through performance optimization\n• Implemented AI-driven features that increased client engagement by 40%\n• Mentored 5 junior developers, 3 of whom were promoted to mid-level positions",
        ]);

        // Skills Section
        $skillsSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'skills',
            'display_order' => 3,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $skillsSection->id,
            'category_name' => 'Frameworks / Back End',
            'skills' => ['Laravel 8+', 'PHP 8+', 'REST', 'Livewire'],
            'display_order' => 1,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $skillsSection->id,
            'category_name' => 'Front End',
            'skills' => ['Vue.js', 'Nuxt', 'Tailwind CSS', 'Alpine.js', 'vanilla JS (ES6+)', 'HTML', 'CSS'],
            'display_order' => 2,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $skillsSection->id,
            'category_name' => 'AI & Automation',
            'skills' => ['LaraChain', 'OpenAI API integration', 'MCP servers'],
            'display_order' => 3,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $skillsSection->id,
            'category_name' => 'DevOps & Cloud',
            'skills' => ['Docker', 'GitHub Actions', 'Ansible', 'Chef', 'CI/CD', 'AWS', 'OVH', 'Linux hardening'],
            'display_order' => 4,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $skillsSection->id,
            'category_name' => 'Data / Caching',
            'skills' => ['MySQL', 'PostgreSQL', 'Redis', 'ElasticSearch'],
            'display_order' => 5,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $skillsSection->id,
            'category_name' => 'Quality',
            'skills' => ['PHPUnit', 'Pest', 'PHPStan'],
            'display_order' => 6,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $skillsSection->id,
            'category_name' => 'Scripting / Tooling',
            'skills' => ['Bash', 'Git', 'Linux server admin'],
            'display_order' => 7,
        ]);

        // Experience Section
        $experienceSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'experience',
            'display_order' => 4,
        ]);

        CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Senior Full-Stack Developer',
            'company_name' => 'StickyPiston Hosting',
            'start_date' => '2024-09-01',
            'end_date' => '2025-07-31',
            'highlights' => [
                'Architected a micro-service delivery system (Laravel, RabbitMQ, Elixir) to automate Minecraft-server provisioning and lifecycle management',
                'Overhauled the e-commerce order flow (Vue & Livewire), preparing the platform for high-volume seasonal launches',
            ],
            'display_order' => 1,
        ]);

        CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Senior PHP Developer',
            'company_name' => 'Wonderful Payments',
            'start_date' => '2024-03-01',
            'end_date' => '2024-09-01',
            'highlights' => [
                'Enhanced the Open Banking gateway, cutting integration errors and stabilising daily throughput',
                'Modernised the customer portal (Tailwind), boosting engagement and average session time',
                'Streamlined staff admin workflows, reducing manual tasks and support queries',
                'Optimised technical SEO, increasing organic traffic 10% in 3 months',
                'Integrated Ghost CMS, enabling marketing to publish content without dev assistance',
            ],
            'display_order' => 2,
        ]);

        CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Director, Developer & Designer',
            'company_name' => 'TechAgency Ltd',
            'start_date' => '2015-08-01',
            'end_date' => '2024-03-01',
            'highlights' => [
                'Founded & scaled a digital agency, securing 20+ clients across various industries',
                'Led end-to-end delivery (sales → discovery → design → build → support) across e-commerce, education, and charity sectors',
                'Built AI-driven workflow tools that reduced client admin and improved data accuracy',
                'Managed a distributed team (developers, designers), instilling DevOps and automated-testing practices',
                'Retained 90% of clients through continuous improvement and transparent communication',
                'Innovated touch screen display solutions for retail environments',
            ],
            'display_order' => 3,
        ]);

        CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Lead LAMP Developer',
            'company_name' => 'My Social Agency (Contract)',
            'start_date' => '2014-11-01',
            'end_date' => '2015-08-01',
            'highlights' => [
                'Led back-end builds for Hunters Estate Agents and KLM Airlines',
                'Migrated high-traffic WordPress sites to MODX, improving security and load times',
                'Introduced Magento across the agency, opening new e-commerce revenue streams',
                'Mentored three developers, raising coding standards and delivery speed',
            ],
            'display_order' => 4,
        ]);

        CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Lead LAMP Developer',
            'company_name' => 'StickyPiston Hosting',
            'start_date' => '2011-11-01',
            'end_date' => '2015-08-01',
            'highlights' => [
                'Engineered an automated Minecraft-server deployment suite (Ansible, Linux, Vue.js)',
                'Cut manual setup steps, boosting deployment speed and reliability',
                'Enhanced monitoring and alerts, reducing downtime incidents',
                'Owned the roadmap and code quality for a dedicated dev team',
            ],
            'display_order' => 5,
        ]);

        CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Lead LAMP Developer',
            'company_name' => 'Onstate',
            'start_date' => '2010-09-01',
            'end_date' => '2011-01-01',
            'highlights' => [
                'Contributed to the ongoing development of the Jimmy Choo e-commerce site, working within the Venda enterprise platform',
                'Delivered responsive, cross-browser front-end components and back-end features using SOLID OOP principles',
                'Collaborated closely with project managers and designers to ensure high end brand alignment',
            ],
            'display_order' => 6,
        ]);

        CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'LAMP Developer',
            'company_name' => 'Chapter Eight',
            'start_date' => '2007-03-01',
            'end_date' => '2010-11-01',
            'highlights' => [
                'Developed core modules for the in-house Odyssey CMS / e-commerce platform',
                'Built a domain-registration tool and bespoke CRM',
            ],
            'display_order' => 7,
        ]);

        CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'LAMP Developer',
            'company_name' => 'Creative Acquisitions',
            'start_date' => '2004-07-01',
            'end_date' => '2007-03-01',
            'highlights' => [
                'Delivered multiple internal websites and utilities',
                'Streamlined workflows, accelerating content updates and reporting',
            ],
            'display_order' => 8,
        ]);

        // Projects Section
        $projectsSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'projects',
            'display_order' => 5,
        ]);

        CvProject::create([
            'cv_section_id' => $projectsSection->id,
            'project_name' => 'Houston - AI-Enhanced Task & Billing',
            'description' => 'Laravel SaaS; OpenAI automates estimates, invoices, and client comms',
            'technologies' => 'Laravel, OpenAI API, Tailwind CSS',
            'display_order' => 1,
        ]);

        CvProject::create([
            'cv_section_id' => $projectsSection->id,
            'project_name' => 'AI-Powered Enneagram Profiler',
            'description' => 'Generates personalised personality reports with OpenAI enhancement',
            'technologies' => 'Laravel, OpenAI API',
            'display_order' => 2,
        ]);

        CvProject::create([
            'cv_section_id' => $projectsSection->id,
            'project_name' => 'ADHD-Friendly Task Manager',
            'description' => 'Focus planner with AI-driven scheduling, task breakdown and a low-distraction UI',
            'technologies' => 'Laravel, OpenAI API, Alpine.js',
            'display_order' => 3,
        ]);

        CvProject::create([
            'cv_section_id' => $projectsSection->id,
            'project_name' => 'The New Manifesto',
            'description' => 'Online art portal with third party drop shipping of physical art products',
            'technologies' => 'Laravel, E-commerce',
            'display_order' => 4,
        ]);

        // Education Section
        $educationSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'education',
            'display_order' => 6,
        ]);

        CvEducation::create([
            'cv_section_id' => $educationSection->id,
            'degree' => 'BA (Hons) Graphic Arts & Design',
            'institution' => 'Leeds Met University',
            'start_year' => 2000,
            'end_year' => 2004,
            'display_order' => 1,
        ]);

        // References Section
        $referencesSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'references',
            'display_order' => 7,
        ]);

        CvReference::create([
            'cv_section_id' => $referencesSection->id,
            'content' => 'Available upon request',
        ]);

        // Job Applications
        JobApplication::create([
            'cv_id' => $cv->id,
            'company_name' => 'Acme Corp',
            'company_website' => 'https://acmecorp.example.com',
            'company_notes' => 'Leading tech company focused on AI solutions',
            'point_of_contact_name' => 'Jane Smith',
            'point_of_contact_email' => 'jane.smith@acmecorp.example.com',
            'send_status' => 'sent',
            'application_status' => 'interviewing',
            'interview_dates' => [
                [
                    'date' => now()->addDays(3)->format('Y-m-d H:i:s'),
                    'type' => 'Technical Interview',
                ],
            ],
            'interview_notes' => 'Prepare for Laravel and AI integration questions',
            'notes' => 'Strong cultural fit, exciting role',
        ]);

        JobApplication::create([
            'cv_id' => $cv->id,
            'company_name' => 'TechStart Ltd',
            'company_website' => 'https://techstart.example.com',
            'point_of_contact_name' => 'John Doe',
            'point_of_contact_email' => 'john@techstart.example.com',
            'send_status' => 'sent',
            'application_status' => 'pending',
            'notes' => 'Startup focusing on Laravel SaaS products',
        ]);

        JobApplication::create([
            'cv_id' => $cv->id,
            'company_name' => 'Global Solutions Inc',
            'company_website' => 'https://globalsolutions.example.com',
            'send_status' => 'draft',
            'application_status' => 'pending',
            'notes' => 'Need to tailor CV for DevOps emphasis',
        ]);
    }
}
