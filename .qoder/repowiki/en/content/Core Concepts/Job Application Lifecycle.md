# Job Application Lifecycle

<cite>
**Referenced Files in This Document**   
- [JobApplication.php](file://app/Models/JobApplication.php)
- [JobApplicationObserver.php](file://app/Observers/JobApplicationObserver.php)
- [JobApplicationForm.php](file://app/Filament/Resources/JobApplications/Schemas/JobApplicationForm.php)
- [JobApplicationsTable.php](file://app/Filament/Resources/JobApplications/Tables/JobApplicationsTable.php)
- [ApplicationsNeedingAction.php](file://app/Filament/Widgets/ApplicationsNeedingAction.php)
- [KeywordCoverageService.php](file://app/Services/KeywordCoverageService.php)
- [PdfSnapshotService.php](file://app/Services/PdfSnapshotService.php)
- [PDFSnapshot.php](file://app/Models/PDFSnapshot.php)
- [Cv.php](file://app/Models/Cv.php)
- [extend_job_applications_table.php](file://database/migrations/2025_10_04_002540_extend_job_applications_table.php)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Core Entity: JobApplication](#core-entity-jobapplication)
3. [Status Workflow](#status-workflow)
4. [Eloquent Relationships](#eloquent-relationships)
5. [Observer Pattern Implementation](#observer-pattern-implementation)
6. [Keyword Coverage Analysis](#keyword-coverage-analysis)
7. [Business Logic: Needs Attention Filter](#business-logic-needs-attention-filter)
8. [Filament Resource Usage Patterns](#filament-resource-usage-patterns)
9. [Conclusion](#conclusion)

## Introduction
The Job Application lifecycle in the CV Builder application centers around the `JobApplication` model as the primary entity for tracking job pursuit activities. This document provides a comprehensive overview of the job application system, detailing its core attributes, status workflow, relationships, business logic, and user interface patterns. The system is designed to support job seekers in managing their applications with features like deadline tracking, follow-up reminders, keyword analysis, and PDF snapshot generation.

## Core Entity: JobApplication
The `JobApplication` model serves as the central entity for tracking job pursuit activities. It contains key attributes that capture essential information about each job opportunity, enabling comprehensive management of the application process.

### Key Attributes
The `JobApplication` model includes the following core attributes:
- `company_name`: The name of the employer (required)
- `job_title`: The role being applied for (required)
- `application_status`: Current stage in the application funnel
- `send_status`: Submission state of the application
- `application_deadline`: Deadline for submitting the application
- `next_action_date`: Date for follow-up or next required action
- `job_description`: Full job specification text for analysis
- `last_activity_at`: Timestamp of the most recent modification

These attributes are defined in the model's `$fillable` array and include appropriate type casting for dates and timestamps. The migration `extend_job_applications_table.php` adds these fields to the database schema with proper indexing for efficient querying on status and next action date.

**Section sources**
- [JobApplication.php](file://app/Models/JobApplication.php#L15-L35)
- [extend_job_applications_table.php](file://database/migrations/2025_10_04_002540_extend_job_applications_table.php#L10-L20)

## Status Workflow
The job application system implements a structured workflow that guides users through the various stages of the job application process. This workflow is represented by two distinct status fields that capture different aspects of the application lifecycle.

### Application Status Workflow
The `application_status` field follows a progression that represents the candidate's position in the hiring funnel:
```
draft → pending → interviewing → offer_received/rejected/accepted/withdrawn
```

The possible values for `application_status` are: 'pending', 'reviewed', 'interviewing', 'offered', 'rejected', 'accepted', and 'withdrawn'. This enum represents the candidate's current position in the hiring process, from initial submission through to final outcome.

### Send Status Workflow
The `send_status` field tracks the submission state of the application with two possible values:
- `draft`: The application has been created but not yet sent
- `sent`: The application has been submitted to the employer

This separation allows users to prepare applications in draft form before officially submitting them, at which point a PDF snapshot is automatically generated as proof of what was sent.

**Section sources**
- [JobApplication.php](file://app/Models/JobApplication.php#L25-L28)
- [JobApplicationForm.php](file://app/Filament/Resources/JobApplications/Schemas/JobApplicationForm.php#L45-L55)

## Eloquent Relationships
The `JobApplication` model establishes several important relationships with other entities in the system, creating a connected data model that supports comprehensive job application management.

### belongsTo CV Relationship
Each job application is associated with a specific CV through the `cv()` relationship method:

```php
public function cv(): BelongsTo
{
    return $this->belongsTo(Cv::class);
}
```

This relationship enables the system to track which tailored CV was used for each application. The relationship is optional, allowing users to create job applications before selecting or creating a CV.

### hasOne PDFSnapshot Relationship
Each job application can have one PDF snapshot that represents the exact version of the CV that was sent:

```php
public function pdfSnapshot(): HasOne
{
    return $this->hasOne(PDFSnapshot::class);
}
```

This relationship is created when the `send_status` changes from 'draft' to 'sent', triggering the generation of a PDF snapshot that serves as immutable proof of what was submitted.

### morphMany for Application Events
Although not fully implemented in the current codebase, the system is designed to support a flexible event system through polymorphic relationships. This would allow tracking of various application events such as interviews scheduled, replies received, and offers made.

**Section sources**
- [JobApplication.php](file://app/Models/JobApplication.php#L40-L55)
- [PDFSnapshot.php](file://app/Models/PDFSnapshot.php#L25-L39)

## Observer Pattern Implementation
The `JobApplicationObserver.php` implements the observer pattern to automatically handle business logic when model events occur, ensuring consistent behavior without cluttering the application code.

### Automatic last_activity_at Updates
The observer automatically updates the `last_activity_at` timestamp whenever a job application is modified:

```php
public function updating(JobApplication $jobApplication): void
{
    $jobApplication->last_activity_at = now();
}
```

This ensures that the `last_activity_at` field always reflects the most recent modification to the application, providing an accurate timeline of user activity.

### PDF Snapshot Creation
When a job application's `send_status` changes to 'sent', the observer triggers the creation of a PDF snapshot:

```php
public function updated(JobApplication $jobApplication): void
{
    if ($jobApplication->wasChanged('send_status') &&
        $jobApplication->send_status === 'sent' &&
        ! $jobApplication->pdfSnapshot) {
        
        $pdfSnapshotService = app(PdfSnapshotService::class);
        $pdfSnapshotService->create($jobApplication);
    }
}
```

This implementation ensures that a PDF snapshot is automatically generated when the user indicates they have sent the application, creating an immutable record of exactly what was submitted.

**Section sources**
- [JobApplicationObserver.php](file://app/Observers/JobApplicationObserver.php#L15-L40)

## Keyword Coverage Analysis
The system includes a sophisticated keyword coverage analysis feature that helps users optimize their CVs for specific job opportunities by identifying missing keywords from job descriptions.

### KeywordCoverageService Implementation
The `KeywordCoverageService` analyzes the job description and CV content to calculate coverage percentage and identify missing keywords:

```php
public function calculateCoverage(string $jobDescription, string $cvContent): array
{
    $jobKeywords = array_unique($this->tokenize($jobDescription));
    $cvKeywords = array_unique($this->tokenize($cvContent));
    
    $matched = array_intersect($jobKeywords, $cvKeywords);
    $missing = array_diff($jobKeywords, $cvKeywords);
    
    $coverage = $totalJobKeywords > 0
        ? (count($matched) / $totalJobKeywords) * 100
        : 0;
        
    return [
        'coverage_percentage' => round($coverage, 2),
        'missing_keywords' => array_values(array_slice($missing, 0, 20)),
        'matched_count' => count($matched),
        'total_job_keywords' => $totalJobKeywords,
    ];
}
```

The service uses text tokenization to extract meaningful keywords while filtering out common stopwords and short tokens.

### Integration with Filament Form
The keyword coverage analysis is integrated directly into the job application form, providing real-time feedback:

```php
Placeholder::make('keyword_coverage')
    ->content(function ($get) {
        $jobDescription = $get('job_description');
        $cvId = $get('cv_id');
        
        if (! $jobDescription || ! $cvId) {
            return 'Add a job description and select a CV to see keyword coverage.';
        }
        
        $cv = \App\Models\Cv::find($cvId);
        $service = app(KeywordCoverageService::class);
        $coverage = $service->calculateCoverage($jobDescription, $cvContent);
        
        return "Coverage: {$percentage}% ({$coverage['matched_count']}/{$coverage['total_job_keywords']} keywords)\n".
               "Top missing keywords: {$missingKeywords}";
    })
```

This integration allows users to immediately see how well their CV matches the job requirements and identify areas for improvement.

**Section sources**
- [KeywordCoverageService.php](file://app/Services/KeywordCoverageService.php#L25-L55)
- [JobApplicationForm.php](file://app/Filament/Resources/JobApplications/Schemas/JobApplicationForm.php#L120-L170)

## Business Logic: Needs Attention Filter
The system implements intelligent business logic to help users prioritize their job applications through the "Needs Attention" filter, which surfaces applications requiring immediate action.

### Filter Criteria
The `scopeNeedsAttention` method in the `JobApplication` model defines the criteria for applications that need attention:

```php
public function scopeNeedsAttention(Builder $query): Builder
{
    return $query->where(function ($q) {
        $q->where(function ($q2) {
            $q2->where('next_action_date', '<=', now())
                ->orWhere('send_status', 'draft')
                ->orWhere(function ($q3) {
                    $q3->whereIn('application_status', ['pending', 'interviewing'])
                        ->whereNull('next_action_date');
                });
        })->whereNotIn('application_status', ['rejected', 'withdrawn']);
    });
}
```

An application needs attention if any of the following conditions are met:
- The `next_action_date` is today or in the past (deadline or follow-up is due)
- The `send_status` is 'draft' (application hasn't been sent)
- The `application_status` is 'pending' or 'interviewing' and no `next_action_date` is set

Applications with `application_status` of 'rejected' or 'withdrawn' are excluded from this filter, as they no longer require active management.

### Widget Implementation
The "Applications Needing Action" widget displays the top 10 applications that need attention:

```php
public function table(Table $table): Table
{
    return $table
        ->query(
            JobApplication::query()
                ->needsAttention()
                ->limit(10)
        )
        // ... columns configuration
}
```

The widget uses color coding to indicate urgency, with overdue actions highlighted in red and upcoming actions in yellow, helping users quickly identify priorities.

**Section sources**
- [JobApplication.php](file://app/Models/JobApplication.php#L57-L75)
- [ApplicationsNeedingAction.php](file://app/Filament/Widgets/ApplicationsNeedingAction.php#L15-L25)

## Filament Resource Usage Patterns
The job application system leverages Filament, a Laravel admin panel package, to provide a rich user interface for managing job applications with consistent patterns across forms and tables.

### Form Layout
The `JobApplicationForm.php` organizes fields into logical sections:
- **Application Details**: Company information and contact details
- **Status & Dates**: Application status, send status, and key dates
- **CV Selection**: Relationship field to select the CV to use
- **Job Description**: Rich text editor for the job posting
- **Interview Details**: Collapsible section for interview information
- **Notes**: Collapsible section for additional notes

The form uses appropriate field types including text inputs, selects, date pickers, and a rich editor, with proper validation rules applied.

### Table Filters and Columns
The `JobApplicationsTable.php` configures a comprehensive table view with:
- Searchable and sortable columns for company name and job title
- Badge columns for status fields with color coding
- Date formatting for deadline and action dates
- Conditional coloring for overdue next action dates

The table includes filters for:
- **Needs Attention**: Ternary filter applying the `needsAttention()` scope
- **Send Status**: Select filter for draft/sent status
- **Application Status**: Select filter for all application statuses

The table is configured to sort by `next_action_date` in ascending order by default, prioritizing applications with upcoming deadlines.

**Section sources**
- [JobApplicationForm.php](file://app/Filament/Resources/JobApplications/Schemas/JobApplicationForm.php#L10-L175)
- [JobApplicationsTable.php](file://app/Filament/Resources/JobApplications/Tables/JobApplicationsTable.php#L10-L100)

## Conclusion
The Job Application lifecycle in the CV Builder application provides a comprehensive system for managing job pursuits with thoughtful design and robust functionality. The `JobApplication` model serves as the central entity, capturing essential information about each opportunity with attributes like company, job title, status, deadlines, and job descriptions. The system implements a clear status workflow that guides users through the application process while maintaining separation between application status and send status.

Key features like the observer pattern ensure consistent behavior by automatically updating timestamps and generating PDF snapshots when applications are sent. The keyword coverage analysis helps users optimize their CVs for specific roles by identifying missing keywords from job descriptions. The "Needs Attention" filter and widget provide intelligent prioritization, surfacing applications that require immediate action based on deadlines, draft status, or pending follow-ups.

The Filament resource implementation provides a user-friendly interface with well-organized forms, informative tables, and practical filters that make managing multiple job applications efficient and effective. Together, these components create a powerful tool that supports job seekers in their pursuit of new opportunities.