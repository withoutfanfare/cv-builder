# Soft Delete and Restore

<cite>
**Referenced Files in This Document**   
- [Cv.php](file://app/Models/Cv.php) - *Updated with SoftDeletes trait and cloneCv method*
- [CvResource.php](file://app/Filament/Resources/Cvs/CvResource.php) - *Modified with soft delete actions*
- [CvsTable.php](file://app/Filament/Resources/Cvs/Tables/CvsTable.php) - *Updated with TrashedFilter and clone action*
- [add_soft_deletes_to_cvs.php](file://database/migrations/2025_10_04_002505_add_soft_deletes_to_cvs.php) - *Added soft delete migration*
- [CVVersion.php](file://app/Models/CVVersion.php) - *New version tracking model*
- [CVVersionResource.php](file://app/Filament/Resources/CVVersions/CVVersionResource.php) - *New resource for version management*
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Soft Deletes Implementation in CV Model](#soft-deletes-implementation-in-cv-model)
3. [Database Migration for Soft Deletes](#database-migration-for-soft-deletes)
4. [User Interface in Filament Resource](#user-interface-in-filament-resource)
5. [Impact on Related Models and Cascading Behavior](#impact-on-related-models-and-cascading-behavior)
6. [Common Issues and Solutions](#common-issues-and-solutions)
7. [Best Practices for Managing Deleted CVs](#best-practices-for-managing-deleted-cvs)
8. [Conclusion](#conclusion)

## Introduction
The soft delete and restore functionality in the CV Builder application enables users to archive CVs without permanently removing them from the database. This ensures data integrity, preserves historical records of job applications and PDF snapshots, and allows for recovery of accidentally deleted CVs. The implementation leverages Laravel's `SoftDeletes` trait and integrates seamlessly with the Filament admin panel, providing a user-friendly interface for managing CV lifecycle actions. The system also incorporates CV versioning to maintain historical snapshots when CVs are cloned or modified.

## Soft Deletes Implementation in CV Model

The `Cv` model implements Laravel's `SoftDeletes` trait to enable soft deletion functionality. When a CV is deleted, the `deleted_at` timestamp is set instead of removing the record from the database. This allows the application to retain all related data while marking the CV as archived.

The `SoftDeletes` trait provides several key methods:
- `delete()`: Sets the `deleted_at` timestamp
- `restore()`: Removes the `deleted_at` timestamp to restore the record
- `trashed()`: Checks if the model has been soft deleted
- `withTrashed()`: Includes soft-deleted records in queries
- `onlyTrashed()`: Returns only soft-deleted records

The `Cv` model also includes a `cloneCv` method that creates a deep copy of the CV and its associated sections before deletion, ensuring version history is preserved. This method operates within a database transaction to ensure data consistency and creates a `CVVersion` snapshot of the original CV before cloning.

**Section sources**
- [Cv.php](file://app/Models/Cv.php#L1-L342) - *Updated with SoftDeletes trait and cloneCv implementation*

## Database Migration for Soft Deletes

The migration file `2025_10_04_002505_add_soft_deletes_to_cvs.php` adds soft delete capability to the `cvs` table by introducing the `deleted_at` column and an index for improved query performance.

```php
Schema::table('cvs', function (Blueprint $table) {
    $table->softDeletes();
    $table->index('deleted_at');
});
```

The `softDeletes()` method creates a nullable `deleted_at` timestamp column that stores the date and time when the record was soft deleted. An index is added to this column to optimize queries that filter based on deletion status, particularly when using the `withTrashed()` and `onlyTrashed()` scopes.

The migration also includes a `down()` method that properly removes the soft delete functionality by dropping both the `deleted_at` column and its index.

**Section sources**
- [add_soft_deletes_to_cvs.php](file://database/migrations/2025_10_04_002505_add_soft_deletes_to_cvs.php#L1-L30) - *Added soft delete migration with index*

## User Interface in Filament Resource

The Filament resource provides a comprehensive interface for managing CVs, including delete and restore actions through the `TrashedFilter` and appropriate table actions.

The `CvsTable` class configures the table with a `TrashedFilter` that allows users to toggle between viewing active and archived CVs:

```php
->filters([
    TrashedFilter::make()
        ->label('Status')
        ->placeholder('Active CVs')
        ->trueLabel('Archived CVs')
        ->falseLabel('Active CVs')
        ->native(false),
])
```

When a user deletes a CV, a confirmation modal displays a warning message explaining that the CV will be archived and that related job applications and PDF snapshots will remain accessible. Archived CVs can be restored by filtering to show trashed records and using the restore action.

The table also includes a "Clone CV" action that creates a copy of the CV before deletion, ensuring version history is maintained. This action triggers the `cloneCv` method on the CV model and creates a version snapshot in the `cv_versions` table.

**Section sources**
- [CvsTable.php](file://app/Filament/Resources/Cvs/Tables/CvsTable.php#L14-L75) - *Updated with TrashedFilter and clone action*
- [CvResource.php](file://app/Filament/Resources/Cvs/CvResource.php#L16-L61) - *Modified with soft delete actions*

## Impact on Related Models and Cascading Behavior

Soft deleting a CV does not affect related models, which remain intact and accessible. This design preserves the integrity of job applications that reference the CV and maintains PDF snapshots that were generated from it.

Key relationships that are preserved:
- `jobApplications`: Job applications continue to reference the archived CV
- `pdfSnapshots`: PDF snapshots remain accessible and downloadable
- `versions`: CV version history is maintained
- All section types (summary, skills, experience, etc.): Related section data remains in the database

The application uses `withTrashed()` when necessary to access archived CVs from related models, ensuring that historical data remains available for reporting and auditing purposes.

This approach follows the requirement specified in the roadmap documentation that CVs should be soft-deleted (archived) to preserve all associated PDF snapshots and application history.

**Section sources**
- [Cv.php](file://app/Models/Cv.php#L1-L342) - *Updated with SoftDeletes trait and relationship methods*

## Common Issues and Solutions

### Issue 1: Restoring a CV with Deleted Related Sections
When restoring a CV, there is a potential issue if related sections have been hard-deleted. The current implementation prevents this by cascading soft deletes to related models only when explicitly configured.

**Solution**: The application does not automatically soft delete related sections. Instead, it relies on the integrity of the data model where sections are only removed when their parent CV is permanently deleted. This ensures that when a CV is restored, all its sections are also available.

### Issue 2: Performance with Large Numbers of Archived CVs
As the number of archived CVs grows, queries may slow down if not properly indexed.

**Solution**: The migration includes an index on the `deleted_at` column to optimize filtering performance. Additionally, the application can implement retention policies to periodically clean up old archived CVs.

### Issue 3: User Confusion Between Delete and Archive
Users may not understand the difference between soft delete and permanent deletion.

**Solution**: The UI clearly labels the action as "Archive" in the confirmation modal and provides explanatory text about what will happen to related data. The filter is labeled "Status" with clear "Active CVs" and "Archived CVs" options.

## Best Practices for Managing Deleted CVs

### Implement Retention Policies
Establish a retention policy for archived CVs based on business requirements. For example, automatically purge CVs that have been archived for more than 5 years:

```php
Cv::onlyTrashed()
    ->where('deleted_at', '<', now()->subYears(5))
    ->forceDelete();
```

### Regular Data Audits
Periodically review archived CVs to ensure data integrity and identify any records that should be permanently removed.

### Clear User Communication
Ensure users understand the implications of archiving a CV:
- Related job applications remain accessible
- PDF snapshots are preserved
- The CV can be restored at any time
- Storage space is still consumed

### Monitor Storage Usage
Track database size and implement alerts when storage reaches certain thresholds, as archived records continue to consume space.

### Backup Strategy
Include archived records in regular backup procedures, as they contain valuable historical data.

## Conclusion
The soft delete and restore functionality in the CV Builder application provides a robust mechanism for managing CV lifecycle while preserving data integrity. By leveraging Laravel's `SoftDeletes` trait and integrating with Filament's admin interface, the application offers users a seamless experience for archiving and restoring CVs. The implementation carefully considers the impact on related models, ensuring that job applications and PDF snapshots remain accessible even after a CV is archived. Following best practices for retention policies and user communication will help maintain optimal performance and user satisfaction.