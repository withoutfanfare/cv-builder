<?php

namespace Database\Seeders;

use App\Models\CoverLetter;
use App\Models\Cv;
use App\Models\CvCustomSection;
use App\Models\CvEducation;
use App\Models\CvExperience;
use App\Models\CvHeaderInfo;
use App\Models\CvProject;
use App\Models\CvSection;
use App\Models\CvSkillCategory;
use App\Models\CvSummary;
use App\Models\JobApplication;
use App\Models\SectionFocusProfile;
use App\Models\SkillEvidence;
use Illuminate\Database\Seeder;

class Phase3DemoSeeder extends Seeder
{
    /**
     * Seed the application's database with a comprehensive Phase 3 demo.
     */
    public function run(): void
    {
        $this->command->info('🌱 Creating comprehensive Phase 3 demo CV...');

        // Create the main CV
        $cv = Cv::create([
            'title' => 'Sarah Johnson - Full Stack Developer',
        ]);

        $this->command->info('✅ Created CV: '.$cv->title);

        // Create header info
        CvHeaderInfo::create([
            'cv_id' => $cv->id,
            'full_name' => 'Sarah Johnson',
            'job_title' => 'Senior Full Stack Developer',
            'email' => 'sarah.johnson@example.com',
            'phone' => '+44 7700 900123',
            'location' => 'London, UK',
            'linkedin_url' => 'https://linkedin.com/in/sarahjohnson',
            'github_url' => 'https://github.com/sarahjohnson',
            'website_url' => 'https://sarahjohnson.dev',
        ]);

        $this->command->info('✅ Created header info');

        // Create Summary Section
        $summarySection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'summary',
            'title' => 'Professional Summary',
            'display_order' => 1,
        ]);

        CvSummary::create([
            'cv_section_id' => $summarySection->id,
            'content' => 'Senior Full Stack Developer with 8+ years of experience building scalable web applications. Expert in React, Laravel, and cloud infrastructure. Proven track record of leading teams and delivering high-impact projects that increased revenue by 40% and improved performance by 60%. Passionate about clean code, testing, and mentoring junior developers.',
        ]);

        $this->command->info('✅ Created summary section');

        // Create Skills Section - Frontend
        $frontendSkillsSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'skills',
            'title' => 'Frontend Development',
            'display_order' => 2,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $frontendSkillsSection->id,
            'category_name' => 'Frontend Technologies',
            'skills' => ['React', 'TypeScript', 'Next.js', 'Vue.js', 'Tailwind CSS', 'Alpine.js', 'Vite'],
            'display_order' => 1,
        ]);

        // Create Skills Section - Backend
        $backendSkillsSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'skills',
            'title' => 'Backend Development',
            'display_order' => 3,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $backendSkillsSection->id,
            'category_name' => 'Backend Technologies',
            'skills' => ['Laravel', 'PHP', 'Node.js', 'PostgreSQL', 'MySQL', 'Redis', 'RESTful APIs'],
            'display_order' => 1,
        ]);

        // Create Skills Section - DevOps
        $devopsSkillsSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'skills',
            'title' => 'DevOps & Tools',
            'display_order' => 4,
        ]);

        CvSkillCategory::create([
            'cv_section_id' => $devopsSkillsSection->id,
            'category_name' => 'DevOps & Infrastructure',
            'skills' => ['Docker', 'AWS', 'CI/CD', 'Git', 'Linux', 'Nginx'],
            'display_order' => 1,
        ]);

        $this->command->info('✅ Created 3 skills sections');

        // Create Experience Section
        $experienceSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'experience',
            'title' => 'Professional Experience',
            'display_order' => 5,
        ]);

        $exp1 = CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Senior Full Stack Developer',
            'company_name' => 'TechCorp Solutions',
            'company_url' => 'https://techcorp.example.com',
            'location' => 'London, UK',
            'start_date' => '2021-03-01',
            'end_date' => null,
            'is_current' => true,
            'highlights' => [
                'Led migration to React & TypeScript, reducing bug reports by 45%',
                'Architected microservices backend with Laravel, handling 10M+ requests/day',
                'Mentored team of 5 junior developers, improving code review efficiency by 60%',
                'Implemented comprehensive testing strategy (PHPUnit, Pest, Jest) achieving 85% coverage',
            ],
            'display_order' => 1,
        ]);

        $exp2 = CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Full Stack Developer',
            'company_name' => 'StartupXYZ',
            'company_url' => 'https://startupxyz.example.com',
            'location' => 'Remote',
            'start_date' => '2019-01-01',
            'end_date' => '2021-02-28',
            'is_current' => false,
            'highlights' => [
                'Built e-commerce platform with Laravel & Vue.js, generating £2M revenue in year one',
                'Optimized database queries reducing average response time from 800ms to 120ms',
                'Implemented Stripe payment integration processing £500K+ monthly transactions',
                'Established CI/CD pipeline with GitHub Actions, deploying 15+ times per week',
            ],
            'display_order' => 2,
        ]);

        $exp3 = CvExperience::create([
            'cv_section_id' => $experienceSection->id,
            'job_title' => 'Junior Full Stack Developer',
            'company_name' => 'Digital Agency Ltd',
            'company_url' => 'https://digitalagency.example.com',
            'location' => 'Manchester, UK',
            'start_date' => '2017-06-01',
            'end_date' => '2018-12-31',
            'is_current' => false,
            'highlights' => [
                'Developed 12+ WordPress and Laravel client websites',
                'Created reusable React component library used across 8 projects',
                'Improved page load times by 40% through optimization and caching strategies',
            ],
            'display_order' => 3,
        ]);

        $this->command->info('✅ Created 3 work experiences');

        // Create Projects Section
        $projectsSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'projects',
            'title' => 'Key Projects',
            'display_order' => 6,
        ]);

        $project1 = CvProject::create([
            'cv_section_id' => $projectsSection->id,
            'project_name' => 'TaskFlow - Project Management SaaS',
            'project_url' => 'https://taskflow.example.com',
            'description' => 'Full-stack SaaS application for project management with real-time collaboration. Built with Laravel, React, and WebSockets. Serves 5000+ active users across 200+ companies.',
            'technologies' => 'Laravel, React, TypeScript, PostgreSQL, Redis, Pusher, Tailwind CSS',
            'display_order' => 1,
        ]);

        $project2 = CvProject::create([
            'cv_section_id' => $projectsSection->id,
            'project_name' => 'DevMetrics - Analytics Dashboard',
            'project_url' => 'https://github.com/sarahjohnson/devmetrics',
            'description' => 'Open-source analytics dashboard for development teams. Integrates with GitHub, Jira, and Slack. 1.2K GitHub stars, used by 50+ companies.',
            'technologies' => 'Next.js, Node.js, MongoDB, Chart.js, Tailwind CSS',
            'display_order' => 2,
        ]);

        $project3 = CvProject::create([
            'cv_section_id' => $projectsSection->id,
            'project_name' => 'E-Commerce API Platform',
            'project_url' => null,
            'description' => 'Scalable REST API platform serving multiple e-commerce storefronts. Handles inventory, orders, payments, and shipping for £10M+ annual GMV.',
            'technologies' => 'Laravel, PostgreSQL, Redis, Stripe API, AWS Lambda, Docker',
            'display_order' => 3,
        ]);

        $this->command->info('✅ Created 3 projects');

        // Create Education Section
        $educationSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'education',
            'title' => 'Education & Certifications',
            'display_order' => 7,
        ]);

        CvEducation::create([
            'cv_section_id' => $educationSection->id,
            'institution' => 'University of Manchester',
            'degree' => 'BSc (Hons) Computer Science - First Class Honours',
            'start_year' => 2013,
            'end_year' => 2016,
            'description' => 'Specialization in Web Technologies and Database Systems. Final year project: Machine Learning for E-commerce Recommendations (Grade: 92%).',
            'display_order' => 1,
        ]);

        CvEducation::create([
            'cv_section_id' => $educationSection->id,
            'institution' => 'AWS',
            'degree' => 'AWS Certified Solutions Architect - Professional',
            'start_year' => 2022,
            'end_year' => 2022,
            'description' => 'Professional certification demonstrating expertise in designing distributed systems on AWS.',
            'display_order' => 2,
        ]);

        $this->command->info('✅ Created 2 education entries');

        // Create Custom Section - Leadership
        $leadershipSection = CvSection::create([
            'cv_id' => $cv->id,
            'section_type' => 'custom',
            'title' => 'Leadership & Community',
            'display_order' => 8,
        ]);

        CvCustomSection::create([
            'cv_section_id' => $leadershipSection->id,
            'content' => "**Technical Lead** - Led architecture decisions for 3 major product launches\n\n**Conference Speaker** - Spoke at PHP UK Conference 2023 on \"Modern Laravel Architecture\"\n\n**Open Source Contributor** - Regular contributor to Laravel ecosystem packages (500+ GitHub contributions)\n\n**Mentor** - Mentored 12 junior developers through company mentorship program",
        ]);

        $this->command->info('✅ Created custom section');

        // ==========================================
        // PHASE 3: Section Focus Profiles
        // ==========================================

        $this->command->info('');
        $this->command->info('🎯 Creating Section Focus Profiles...');

        // Profile 1: Frontend Developer Focus
        $frontendProfile = SectionFocusProfile::create([
            'cv_id' => $cv->id,
            'name' => 'Frontend Developer Focus',
            'included_section_ids' => [
                $summarySection->id,
                $frontendSkillsSection->id,
                $projectsSection->id,
                $experienceSection->id,
                $educationSection->id,
            ],
            'section_order' => [
                $summarySection->id,
                $frontendSkillsSection->id,
                $projectsSection->id,
                $experienceSection->id,
                $educationSection->id,
            ],
        ]);

        $this->command->info('✅ Created "Frontend Developer Focus" profile');

        // Profile 2: Backend/Full Stack Focus
        $backendProfile = SectionFocusProfile::create([
            'cv_id' => $cv->id,
            'name' => 'Backend/Full Stack Focus',
            'included_section_ids' => [
                $summarySection->id,
                $backendSkillsSection->id,
                $devopsSkillsSection->id,
                $experienceSection->id,
                $projectsSection->id,
                $educationSection->id,
            ],
            'section_order' => [
                $summarySection->id,
                $backendSkillsSection->id,
                $devopsSkillsSection->id,
                $experienceSection->id,
                $projectsSection->id,
                $educationSection->id,
            ],
        ]);

        $this->command->info('✅ Created "Backend/Full Stack Focus" profile');

        // Profile 3: Leadership/Senior Role Focus
        $leadershipProfile = SectionFocusProfile::create([
            'cv_id' => $cv->id,
            'name' => 'Leadership/Senior Role',
            'included_section_ids' => [
                $summarySection->id,
                $leadershipSection->id,
                $experienceSection->id,
                $backendSkillsSection->id,
                $frontendSkillsSection->id,
                $educationSection->id,
            ],
            'section_order' => [
                $summarySection->id,
                $leadershipSection->id,
                $experienceSection->id,
                $backendSkillsSection->id,
                $frontendSkillsSection->id,
                $educationSection->id,
            ],
        ]);

        $this->command->info('✅ Created "Leadership/Senior Role" profile');

        // ==========================================
        // PHASE 3: Skill Evidence
        // ==========================================

        $this->command->info('');
        $this->command->info('🔗 Creating Skill Evidence links...');

        // Link React skill to experiences and projects
        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'React',
            'evidenceable_type' => CvExperience::class,
            'evidenceable_id' => $exp1->id,
            'notes' => 'Led migration to React & TypeScript at TechCorp, reducing bug reports by 45%',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'React',
            'evidenceable_type' => CvExperience::class,
            'evidenceable_id' => $exp3->id,
            'notes' => 'Created reusable React component library used across 8 projects at Digital Agency',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'React',
            'evidenceable_type' => CvProject::class,
            'evidenceable_id' => $project1->id,
            'notes' => 'Built TaskFlow SaaS frontend with React, serving 5000+ active users',
        ]);

        // Link Laravel skill
        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'Laravel',
            'evidenceable_type' => CvExperience::class,
            'evidenceable_id' => $exp1->id,
            'notes' => 'Architected microservices backend with Laravel, handling 10M+ requests/day',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'Laravel',
            'evidenceable_type' => CvExperience::class,
            'evidenceable_id' => $exp2->id,
            'notes' => 'Built e-commerce platform with Laravel, generating £2M revenue in year one',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'Laravel',
            'evidenceable_type' => CvProject::class,
            'evidenceable_id' => $project1->id,
            'notes' => 'TaskFlow SaaS backend built with Laravel 11',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'Laravel',
            'evidenceable_type' => CvProject::class,
            'evidenceable_id' => $project3->id,
            'notes' => 'E-Commerce API Platform serving £10M+ annual GMV',
        ]);

        // Link TypeScript skill
        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'TypeScript',
            'evidenceable_type' => CvExperience::class,
            'evidenceable_id' => $exp1->id,
            'notes' => 'Led migration to TypeScript, improving code quality and reducing bugs',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'TypeScript',
            'evidenceable_type' => CvProject::class,
            'evidenceable_id' => $project1->id,
            'notes' => 'TaskFlow built with TypeScript for type safety',
        ]);

        // Link PostgreSQL skill
        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'PostgreSQL',
            'evidenceable_type' => CvExperience::class,
            'evidenceable_id' => $exp1->id,
            'notes' => 'Optimized PostgreSQL queries for 10M+ daily requests',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'PostgreSQL',
            'evidenceable_type' => CvProject::class,
            'evidenceable_id' => $project1->id,
            'notes' => 'TaskFlow uses PostgreSQL for data persistence',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'PostgreSQL',
            'evidenceable_type' => CvProject::class,
            'evidenceable_id' => $project3->id,
            'notes' => 'E-Commerce API uses PostgreSQL for transactional data',
        ]);

        // Link Next.js skill
        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'Next.js',
            'evidenceable_type' => CvProject::class,
            'evidenceable_id' => $project2->id,
            'notes' => 'DevMetrics analytics dashboard built with Next.js, 1.2K GitHub stars',
        ]);

        // Link Docker/AWS skills
        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'Docker',
            'evidenceable_type' => CvExperience::class,
            'evidenceable_id' => $exp2->id,
            'notes' => 'Containerized applications with Docker for consistent deployments',
        ]);

        SkillEvidence::create([
            'cv_id' => $cv->id,
            'skill_name' => 'AWS',
            'evidenceable_type' => CvProject::class,
            'evidenceable_id' => $project3->id,
            'notes' => 'Deployed E-Commerce API on AWS using Lambda, EC2, and RDS',
        ]);

        $this->command->info('✅ Created 15 skill evidence links');

        // ==========================================
        // PHASE 3: Job Applications & Cover Letters
        // ==========================================

        $this->command->info('');
        $this->command->info('💼 Creating Job Applications with Cover Letters...');

        // Job Application 1: Frontend Role at Acme Corp
        $jobApp1 = JobApplication::create([
            'cv_id' => $cv->id,
            'company_name' => 'Acme Corporation',
            'company_website' => 'https://acme.example.com',
            'company_notes' => 'Leading fintech company, great engineering culture, 500+ employees',
            'point_of_contact_name' => 'Emily Chen',
            'point_of_contact_email' => 'emily.chen@acme.example.com',
            'job_title' => 'Senior React Developer',
            'source' => 'LinkedIn',
            'application_deadline' => now()->addDays(14),
            'next_action_date' => now()->addDays(3),
            'job_description' => "Senior React Developer\n\nWe're seeking a React expert with TypeScript experience to join our frontend team.\n\nYou'll work with React, Next.js, TypeScript, and our microservices backend. Experience with large-scale applications and team leadership preferred.",
            'send_status' => 'draft',
            'application_status' => 'pending',
            'last_activity_at' => now(),
        ]);

        // Cover Letter Version 1 - Technical Tone
        $coverLetter1v1 = CoverLetter::create([
            'job_application_id' => $jobApp1->id,
            'template' => "Dear {{hiring_manager_name}},\n\nI am writing to express my interest in the {{role_title}} position at {{company_name}}. {{value_prop}}.\n\nRecent Achievement:\n{{recent_win}}\n\nRelevant Experience:\n{{relevant_experience}}\n\nI noticed your job posting emphasizes {{key_requirement}}. {{how_i_match}}.\n\n{{closing}}\n\nBest regards,\nSarah Johnson",
            'body' => "Dear Emily Chen,\n\nI am writing to express my interest in the Senior React Developer position at Acme Corporation. I bring 8+ years of full stack development experience with deep expertise in React and TypeScript.\n\nRecent Achievement:\nAt TechCorp Solutions, I led our migration to React & TypeScript, which reduced bug reports by 45% and improved development velocity significantly.\n\nRelevant Experience:\nI've architected and built three production React applications serving 15,000+ users combined, including TaskFlow (a SaaS project management platform with 5,000+ active users).\n\nI noticed your job posting emphasizes large-scale applications and team leadership. In my current role, I mentor a team of 5 developers and handle architecture for applications serving 10M+ daily requests.\n\nI'm excited about the opportunity to bring my technical leadership and React expertise to Acme's fintech platform.\n\nBest regards,\nSarah Johnson",
            'tone' => 'technical',
            'is_sent' => false,
        ]);

        // Cover Letter Version 2 - Enthusiastic Tone
        $coverLetter1v2 = CoverLetter::create([
            'job_application_id' => $jobApp1->id,
            'template' => "Dear {{hiring_manager_name}},\n\nI am thrilled to apply for the {{role_title}} position at {{company_name}}! {{value_prop}}.\n\nWhat excites me most:\n{{excitement}}\n\nMy proudest achievement:\n{{recent_win}}\n\n{{closing}}\n\nExcitedly yours,\nSarah Johnson",
            'body' => "Dear Emily Chen,\n\nI am thrilled to apply for the Senior React Developer position at Acme Corporation! I absolutely love building exceptional user experiences with React, and Acme's mission to revolutionize fintech resonates deeply with me.\n\nWhat excites me most:\nThe opportunity to work on large-scale React applications that impact thousands of users daily. I'm passionate about performance optimization, clean architecture, and creating delightful UIs.\n\nMy proudest achievement:\nLeading our React & TypeScript migration at TechCorp - seeing bug reports drop by 45% while team productivity soared was incredibly rewarding!\n\nI'd be honored to bring my energy, expertise, and collaborative spirit to the Acme engineering team.\n\nExcitedly yours,\nSarah Johnson",
            'tone' => 'enthusiastic',
            'is_sent' => false,
        ]);

        $this->command->info('✅ Created Job Application 1: Acme Corp (2 cover letter versions)');

        // Job Application 2: Full Stack Role at TechStartup
        $jobApp2 = JobApplication::create([
            'cv_id' => $cv->id,
            'company_name' => 'TechStartup Inc',
            'company_website' => 'https://techstartup.example.com',
            'company_notes' => 'Series B startup, 50 employees, fast-growing SaaS product',
            'point_of_contact_name' => 'Michael Roberts',
            'point_of_contact_email' => 'michael@techstartup.example.com',
            'job_title' => 'Lead Full Stack Engineer',
            'source' => 'AngelList',
            'application_deadline' => now()->addDays(7),
            'next_action_date' => now()->addDays(2),
            'job_description' => "Lead Full Stack Engineer\n\nWe're looking for an experienced full stack engineer to help us scale our SaaS platform.\n\nYou'll work with Laravel, React, PostgreSQL, and AWS. We need someone who can architect scalable systems and mentor junior developers. Startup experience highly valued.",
            'send_status' => 'sent',
            'application_status' => 'interviewing',
            'last_activity_at' => now()->subDays(3),
        ]);

        // Cover Letter - Formal Tone (SENT)
        $coverLetter2 = CoverLetter::create([
            'job_application_id' => $jobApp2->id,
            'template' => "Dear {{hiring_manager_name}},\n\nI am writing to apply for the {{role_title}} position at {{company_name}}. {{value_prop}}.\n\nKey qualifications:\n- {{qualification_1}}\n- {{qualification_2}}\n- {{qualification_3}}\n\nI am particularly drawn to {{company_name}} because {{why_company}}.\n\nThank you for your consideration.\n\nSincerely,\nSarah Johnson",
            'body' => "Dear Michael Roberts,\n\nI am writing to apply for the Lead Full Stack Engineer position at TechStartup Inc. I bring 8+ years of full stack development experience with a proven track record in Laravel, React, and cloud infrastructure.\n\nKey qualifications:\n- Built and scaled SaaS platforms serving 5,000+ users with Laravel and React\n- Architected systems handling 10M+ daily requests with 99.9% uptime\n- Mentored teams of 5+ developers, establishing best practices and improving code quality\n\nI am particularly drawn to TechStartup Inc because of your focus on rapid innovation and the opportunity to make significant architectural decisions in a growing company.\n\nThank you for your consideration.\n\nSincerely,\nSarah Johnson",
            'tone' => 'formal',
            'is_sent' => true,
            'sent_at' => now()->subDays(5),
        ]);

        $this->command->info('✅ Created Job Application 2: TechStartup Inc (sent cover letter)');

        // Job Application 3: Backend Role at BigCorp
        $jobApp3 = JobApplication::create([
            'cv_id' => $cv->id,
            'company_name' => 'BigCorp International',
            'company_website' => 'https://bigcorp.example.com',
            'company_notes' => 'Fortune 500 company, excellent benefits, strong engineering practices',
            'point_of_contact_name' => 'James Wilson',
            'point_of_contact_email' => 'james.wilson@bigcorp.example.com',
            'job_title' => 'Principal Backend Engineer',
            'source' => 'Company Website',
            'application_deadline' => now()->addDays(21),
            'next_action_date' => now()->addDays(7),
            'job_description' => "Principal Backend Engineer\n\nLooking for a principal engineer to lead our backend architecture.\n\nDeep expertise in PHP, Laravel, PostgreSQL, and microservices required. You'll define technical standards and mentor senior engineers. Leadership experience essential.",
            'send_status' => 'draft',
            'application_status' => 'pending',
            'last_activity_at' => now(),
        ]);

        // Cover Letter - Leadership Tone
        CoverLetter::create([
            'job_application_id' => $jobApp3->id,
            'template' => "Dear {{hiring_manager_name}},\n\nI am excited to apply for the {{role_title}} position at {{company_name}}.\n\nLeadership Experience:\n{{leadership_experience}}\n\nTechnical Expertise:\n{{technical_expertise}}\n\nImpact:\n{{impact}}\n\nI look forward to discussing how I can contribute to {{company_name}}'s continued success.\n\nBest regards,\nSarah Johnson",
            'body' => "Dear James Wilson,\n\nI am excited to apply for the Principal Backend Engineer position at BigCorp International.\n\nLeadership Experience:\nI currently lead technical architecture decisions for a team of 15 engineers, establishing coding standards, reviewing designs, and mentoring senior developers. I've successfully led 3 major product launches and regularly speak at conferences on modern Laravel architecture.\n\nTechnical Expertise:\nWith 8+ years of Laravel development, I've architected microservices systems handling 10M+ requests daily. My expertise spans database optimization, API design, caching strategies, and cloud infrastructure on AWS.\n\nImpact:\nMy architectural decisions have resulted in:\n- 60% improvement in API response times\n- 45% reduction in production bugs through TypeScript adoption\n- £2M+ revenue generated through platforms I've built\n\nI look forward to discussing how I can contribute to BigCorp International's continued success.\n\nBest regards,\nSarah Johnson",
            'tone' => 'leadership',
            'is_sent' => false,
        ]);

        $this->command->info('✅ Created Job Application 3: BigCorp (leadership cover letter)');

        // ==========================================
        // Summary
        // ==========================================

        $this->command->info('');
        $this->command->info('🎉 Demo data created successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('  • 1 CV: Sarah Johnson - Full Stack Developer');
        $this->command->info('  • 8 Sections: Summary, 3 Skills, Experience, Projects, Education, Custom');
        $this->command->info('  • 3 Work Experiences');
        $this->command->info('  • 3 Projects');
        $this->command->info('  • 2 Education entries');
        $this->command->info('  • 3 Section Focus Profiles (Frontend, Backend, Leadership)');
        $this->command->info('  • 15 Skill Evidence links (React, Laravel, TypeScript, PostgreSQL, etc.)');
        $this->command->info('  • 3 Job Applications with 5 Cover Letters (different tones)');
        $this->command->info('');
        $this->command->info('🎯 Phase 3 Features to Test:');
        $this->command->info('  1. Section Focus Profiles - Apply different profiles to CV');
        $this->command->info('  2. Skill Evidence - View which skills have most proof');
        $this->command->info('  3. Cover Letters - Compare versions, test A/B');
        $this->command->info('  4. Keyword Scoring - Analyze job descriptions');
        $this->command->info('');
    }
}
