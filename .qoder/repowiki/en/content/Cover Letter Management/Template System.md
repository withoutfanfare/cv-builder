# Template System

<cite>
**Referenced Files in This Document**   
- [CoverLetter.php](file://app/Models/CoverLetter.php)
- [CoverLettersRelationManager.php](file://app/Filament/Resources/JobApplications/RelationManagers/CoverLettersRelationManager.php)
- [create_cover_letters_table.php](file://database/migrations/2025_10_04_101841_create_cover_letters_table.php)
- [CoverLetterService.php](file://app/Services/CoverLetterService.php)
- [cover-letters.md](file://specs/002-roadmap-md/contracts-phase3/cover-letters.md)
- [USER-GUIDE-PHASE3.md](file://specs/002-roadmap-md/USER-GUIDE-PHASE3.md)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Database Schema](#database-schema)
3. [Template Field Purpose](#template-field-purpose)
4. [Template and Body Field Relationship](#template-and-body-field-relationship)
5. [Template Creation and Management](#template-creation-and-management)
6. [Template Syntax and Variables](#template-syntax-and-variables)
7. [Business Rules](#business-rules)
8. [Common Issues and Troubleshooting](#common-issues-and-troubleshooting)
9. [Migration and Data Model](#migration-and-data-model)

## Introduction
The Template System in the CV Builder application enables users to create reusable cover letter templates with dynamic variable interpolation. This system supports efficient customization of cover letters across multiple job applications by using mustache-style `{{variables}}` that automatically populate with relevant data. The design allows both templated and freeform letter creation, supporting A/B testing through versioning and tone selection. This document details the implementation, usage, and technical specifications of the template system.

## Database Schema
The cover_letters table stores both the source template and rendered body for each cover letter. The schema is designed to support versioning, tone tracking, and immutable sent records.

```mermaid
erDiagram
COVER_LETTERS {
bigint id PK
bigint job_application_id FK
text template
text body UK
enum tone
int version
boolean is_sent
timestamp sent_at
timestamp created_at
timestamp updated_at
}
JOB_APPLICATIONS ||--o{ COVER_LETTERS : "has many"
```

**Diagram sources**
- [create_cover_letters_table.php](file://database/migrations/2025_10_04_101841_create_cover_letters_table.php#L10-L30)

**Section sources**
- [create_cover_letters_table.php](file://database/migrations/2025_10_04_101841_create_cover_letters_table.php#L10-L30)

## Template Field Purpose
The template field in the CoverLetter model stores the original source template containing mustache-style `{{variables}}` placeholders. This field is nullable to accommodate users who prefer writing cover letters from scratch without templates. When present, the template serves as the blueprint for generating personalized cover letters by replacing variables with actual values from the job application context and user inputs. The template is preserved to enable future editing and reuse across different applications.

**Section sources**
- [CoverLetter.php](file://app/Models/CoverLetter.php#L15-L20)
- [data-model-phase3.md](file://specs/002-roadmap-md/data-model-phase3.md#L74-L122)

## Template and Body Field Relationship
The template and body fields work together to provide both flexibility and permanence in cover letter management. The template field (nullable text) contains the editable source with `{{variables}}`, while the body field (not null text) stores the final rendered content after variable interpolation. When a cover letter is created or updated, the system processes the template through the CoverLetterService::interpolate() method, replacing all valid variables with their corresponding values to generate the body. This separation ensures that the original template remains available for future modifications while preserving an immutable record of what was actually sent.

```mermaid
flowchart TD
A["User creates/modifies template"] --> B["System identifies {{variables}}"]
B --> C["Fetch values from JobApplication + user inputs"]
C --> D["Replace {{variables}} with actual values"]
D --> E["Store original template"]
D --> F["Store rendered body"]
E --> G["Template available for future edits"]
F --> H["Body preserved as sent record"]
```

**Diagram sources**
- [CoverLetterService.php](file://app/Services/CoverLetterService.php#L10-L20)
- [CoverLetter.php](file://app/Models/CoverLetter.php#L15-L20)

**Section sources**
- [CoverLetter.php](file://app/Models/CoverLetter.php#L15-L20)
- [CoverLetterService.php](file://app/Services/CoverLetterService.php#L10-L20)

## Template Creation and Management
Templates are created and managed through the Filament CoverLettersRelationManager form, which provides a user-friendly interface for template development. The form includes a textarea field specifically designed for template creation, supporting variable placeholders with helpful guidance. Users can access this form when editing a job application, navigating to the Cover Letters tab, and clicking Create. The form includes real-time validation and helper text that lists available variables like `{{company_name}}` and `{{role_title}}`. After saving, the system automatically generates the rendered body while preserving the original template for future reference or modification.

```mermaid
flowchart TD
A["Navigate to Job Application"] --> B["Open Cover Letters tab"]
B --> C["Click Create button"]
C --> D["Enter template with {{variables}}"]
D --> E["Fill variable values"]
E --> F["Select tone"]
F --> G["System generates preview"]
G --> H["Save to create cover letter"]
H --> I["Template stored in database"]
H --> J["Body rendered and stored"]
```

**Diagram sources**
- [CoverLettersRelationManager.php](file://app/Filament/Resources/JobApplications/RelationManagers/CoverLettersRelationManager.php#L25-L50)

**Section sources**
- [CoverLettersRelationManager.php](file://app/Filament/Resources/JobApplications/RelationManagers/CoverLettersRelationManager.php#L25-L50)

## Template Syntax and Variables
The template system uses simple mustache-style `{{variable}}` syntax for variable interpolation, making it intuitive and user-friendly. The system supports several predefined variables that automatically populate from job application data or user inputs. Common variables include `{{company_name}}` and `{{role_title}}` which are pulled from the associated job application, while `{{value_prop}}` and `{{recent_win}}` are user-provided per letter. Custom variables like `{{key_requirement}}` and `{{relevant_experience}}` can also be defined to address specific job requirements. The syntax requires exact matching of variable names (case-sensitive) and proper double curly brace delimiters.

```mermaid
flowchart TD
A["Template Syntax Rules"] --> B["Double curly braces: {{variable}}"]
A --> C["No spaces in variable names"]
A --> D["Case-sensitive matching"]
A --> E["No special characters"]
F["Available Variables"] --> G["{{company_name}} - from JobApplication"]
F --> H["{{role_title}} - from JobApplication.job_title"]
F --> I["{{value_prop}} - user-provided"]
F --> J["{{recent_win}} - user-provided"]
F --> K["{{key_requirement}} - custom"]
F --> L["{{relevant_experience}} - custom"]
```

**Diagram sources**
- [USER-GUIDE-PHASE3.md](file://specs/002-roadmap-md/USER-GUIDE-PHASE3.md#L198-L366)
- [research-phase3.md](file://specs/002-roadmap-md/research-phase3.md#L58-L88)

**Section sources**
- [USER-GUIDE-PHASE3.md](file://specs/002-roadmap-md/USER-GUIDE-PHASE3.md#L198-L366)

## Business Rules
The template system enforces several business rules to maintain data integrity and support effective cover letter management. The most important rule is that templates can be null, allowing users to write letters from scratch without using templates. When templates are used, the system preserves them for future editing while storing the rendered body as a permanent record. Sent cover letters become immutable - they cannot be edited or deleted once marked as sent. Version numbers automatically increment for each new cover letter within a job application, enabling A/B testing of different approaches. Only one cover letter per application can be marked as sent at any time.

```mermaid
stateDiagram-v2
[*] --> Draft
Draft --> Sent : Mark as sent
Sent --> [*] : Final state
note right of Sent
Immutable : cannot be
edited or deleted
end note
classDef draftStyle fill : #f9f,stroke : #333;
classDef sentStyle fill : #bbf,stroke : #333;
class Draft draftStyle
class Sent sentStyle
```

**Diagram sources**
- [CoverLetter.php](file://app/Models/CoverLetter.php#L30-L45)
- [data-model-phase3.md](file://specs/002-roadmap-md/data-model-phase3.md#L74-L122)

**Section sources**
- [CoverLetter.php](file://app/Models/CoverLetter.php#L30-L45)

## Common Issues and Troubleshooting
Common issues with the template system typically involve variable syntax errors or missing values. The most frequent problem occurs when variables are not properly formatted with double curly braces (`{{variable}}`), or when there are typos in variable names. Since variable matching is case-sensitive, `{{Company_Name}}` will not match `{{company_name}}`. When variables are missing from the input array, the system preserves the placeholder in the rendered output rather than failing, which helps identify missing data. Users should verify that all variables in their template are accounted for in the variables array and that names match exactly.

```mermaid
flowchart TD
A["Troubleshooting Guide"] --> B["Variable not replaced?"]
B --> C{"Check syntax"}
C --> |Correct| D["{{company_name}}"]
C --> |Incorrect| E["{company_name} or {{ company_name }}"]
B --> F{"Check spelling"}
F --> |Exact match| G["company_name"]
F --> |Mismatch| H["CompanyName or company-name"]
B --> I{"Check case"}
I --> |Lowercase| J["company_name"]
I --> |Uppercase| K["Company_Name"]
B --> L["Missing variable kept as placeholder"]
```

**Diagram sources**
- [USER-GUIDE-PHASE3.md](file://specs/002-roadmap-md/USER-GUIDE-PHASE3.md#L617-L635)
- [CoverLetterService.php](file://app/Services/CoverLetterService.php#L10-L20)

**Section sources**
- [USER-GUIDE-PHASE3.md](file://specs/002-roadmap-md/USER-GUIDE-PHASE3.md#L617-L635)

## Migration and Data Model
The cover_letters table was created through the 2025_10_04_101841_create_cover_letters_table.php migration, which defines the complete schema for the template system. The data model specification includes foreign key constraints to ensure referential integrity with job applications, cascading deletion, and appropriate indexing for performance. The model implements Eloquent relationships and lifecycle hooks to enforce business rules, such as auto-incrementing version numbers and preventing modifications to sent cover letters. The CoverLetter model's boot method contains creating and updating observers that handle versioning and immutability rules automatically.

```mermaid
classDiagram
class CoverLetter {
+job_application_id
+template
+body
+tone
+version
+is_sent
+sent_at
+interpolateVariables()
+preventUpdateWhenSent()
+autoIncrementVersion()
}
class JobApplication {
+coverLetters()
+getLatestCoverLetter()
+getSentCoverLetter()
}
CoverLetter --> JobApplication : "belongsTo"
JobApplication --> CoverLetter : "hasMany"
```

**Diagram sources**
- [create_cover_letters_table.php](file://database/migrations/2025_10_04_101841_create_cover_letters_table.php#L10-L30)
- [CoverLetter.php](file://app/Models/CoverLetter.php#L10-L50)

**Section sources**
- [create_cover_letters_table.php](file://database/migrations/2025_10_04_101841_create_cover_letters_table.php#L10-L30)
- [CoverLetter.php](file://app/Models/CoverLetter.php#L10-L50)