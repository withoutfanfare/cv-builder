# CV Versioning System

<cite>
**Referenced Files in This Document**   
- [CVVersion.php](file://app/Models/CVVersion.php)
- [Cv.php](file://app/Models/Cv.php)
- [CVVersionResource.php](file://app/Filament/Resources/CVVersions/CVVersionResource.php)
- [CVVersionForm.php](file://app/Filament/Resources/CVVersions/Schemas/CVVersionForm.php)
- [CVVersionInfolist.php](file://app/Filament/Resources/CVVersions/Schemas/CVVersionInfolist.php)
- [create_cv_versions_table.php](file://database/migrations/2025_10_04_002612_create_cv_versions_table.php)
- [CvCloningTest.php](file://tests/Feature/CvCloningTest.php)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Database Schema](#database-schema)
3. [Eloquent Relationships](#eloquent-relationships)
4. [Snapshot Data Structure](#snapshot-data-structure)
5. [Cloning Workflow](#cloning-workflow)
6. [Filament Interface](#filament-interface)
7. [Use Cases](#use-cases)
8. [Performance Considerations](#performance-considerations)

## Introduction
The CV Versioning System provides a mechanism to capture immutable snapshots of CVs at specific points in time, primarily when cloning a CV or before significant changes. This system enables historical tracking of CV states, allowing users to compare versions or restore previous states when needed. The versioning functionality is tightly integrated with the CV cloning process, automatically creating a snapshot of the original CV before the clone is created.

**Section sources**
- [cv_builder_spec.md](file://cv_builder_spec.md#L0-L192)
- [ROADMAP.md](file://ROADMAP.md#L0-L190)

## Database Schema
The CV versioning system uses a dedicated `cv_versions` table to store historical snapshots. Each record captures the complete state of a CV at a specific point in time.

| Column | Type | Constraints | Purpose |
|--------|------|-------------|---------|
| id | bigint | PK, auto-increment | Unique identifier |
| cv_id | bigint | FK → cvs.id, onDelete CASCADE | Parent CV |
| snapshot_json | json | required | Full CV data serialized |
| reason | string(255) | required | Why snapshot created |
| created_at | timestamp | auto | When snapshot taken |

The migration file defines the schema with proper constraints and indexing:

```php
Schema::create('cv_versions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cv_id')->constrained()->cascadeOnDelete();
    $table->json('snapshot_json');
    $table->string('reason');
    $table->timestamp('created_at');
    $table->index('cv_id');
});
```

**Section sources**
- [create_cv_versions_table.php](file://database/migrations/2025_10_04_002612_create_cv_versions_table.php#L0-L31)
- [data-model.md](file://specs/002-roadmap-md/data-model.md#L126-L171)

## Eloquent Relationships
The system implements a standard parent-child relationship between CVs and their versions using Eloquent relationships.

### CV Model
The `Cv` model defines a one-to-many relationship with `CVVersion`, allowing access to all versions of a CV:

```php
public function versions(): HasMany
{
    return $this->hasMany(CVVersion::class);
}
```

### CVVersion Model
The `CVVersion` model defines a belongs-to relationship with the parent `Cv`:

```php
public function cv(): BelongsTo
{
    return $this->belongsTo(Cv::class);
}
```

This bidirectional relationship enables easy navigation between a CV and its historical versions, as well as retrieving the parent CV from any version snapshot.

**Section sources**
- [Cv.php](file://app/Models/Cv.php#L180-L184)
- [CVVersion.php](file://app/Models/CVVersion.php#L27-L29)

## Snapshot Data Structure
The `snapshot_json` field stores a complete serialized representation of the CV state at the time of snapshot creation. This includes all sections and their content, preserving the full structure of the CV.

### JSON Serialization
The `CVVersion` model uses Eloquent's casting functionality to handle JSON data:

```php
protected $casts = [
    'snapshot_json' => 'array',
    'created_at' => 'datetime',
];
```

The `snapshot_json` field is cast to an array, allowing Laravel to automatically serialize and deserialize the JSON data when interacting with the database.

### Example Snapshot
A snapshot contains the complete CV structure including all sections:

```json
{
  "id": 123,
  "title": "Software Engineer CV",
  "sections": [
    {
      "id": 1,
      "type": "experience",
      "title": "Work Experience",
      "content": {...},
      "order": 1
    }
  ],
  "created_at": "2025-10-01T10:00:00Z"
}
```

**Section sources**
- [CVVersion.php](file://app/Models/CVVersion.php#L20-L24)
- [data-model.md](file://specs/002-roadmap-md/data-model.md#L126-L171)

## Cloning Workflow
The CV cloning process automatically triggers version creation, ensuring that a snapshot is captured before any modifications are made.

### Clone Process
When a CV is cloned, the system follows this workflow:
1. Create a version snapshot of the original CV
2. Create a new CV entity with a deep copy of all data
3. Return the cloned CV for further editing

The `cloneCv` method in the `Cv` model handles this process within a database transaction:

```php
public function cloneCv(string $reason = 'manual clone'): Cv
{
    return DB::transaction(function () use ($reason) {
        // Create version snapshot
        CVVersion::create([
            'cv_id' => $this->id,
            'snapshot_json' => $this->toArray(),
            'reason' => $reason,
            'created_at' => now(),
        ]);

        // Clone the CV and all related data
        // ... deep copy implementation
    });
}
```

### Automatic Snapshot Creation
The versioning system automatically creates snapshots with appropriate reasons:
- When cloning: "cloned from CV #{id}"
- Before significant changes: Custom reason provided by user
- For historical tracking: Timestamped records of CV state

The cloning tests verify that snapshots are created correctly:

```php
test('clone creates cv version snapshot', function () {
    $originalCv = Cv::factory()->create(['title' => 'Original CV']);
    $clonedCv = $originalCv->cloneCv('Testing clone feature');

    $version = CVVersion::where('cv_id', $originalCv->id)
        ->where('reason', 'Testing clone feature')
        ->first();

    expect($version)->not->toBeNull()
        ->and($version->snapshot_json)->toBeArray();
});
```

**Section sources**
- [Cv.php](file://app/Models/Cv.php#L186-L220)
- [CvCloningTest.php](file://tests/Feature/CvCloningTest.php#L36-L67)

## Filament Interface
The Filament admin interface provides tools for viewing and managing CV versions through the `CVVersionResource`.

### Resource Configuration
The `CVVersionResource` configures the admin interface for version management:

```php
class CVVersionResource extends Resource
{
    protected static ?string $model = CVVersion::class;
    
    public static function table(Table $table): Table
    {
        return CVVersionsTable::configure($table);
    }
    
    public static function infolist(Schema $schema): Schema
    {
        return CVVersionInfolist::configure($schema);
    }
}
```

### Table View
The table view displays key information about versions:
- CV title (from relationship)
- Reason for snapshot creation
- Creation timestamp (sortable)
- View action for detailed inspection

### Detail View
The infolist configuration defines how version details are displayed:

```php
class CVVersionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cv.title')
                    ->label('Cv'),
                TextEntry::make('snapshot_json')
                    ->columnSpanFull(),
                TextEntry::make('reason'),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}
```

The detail view shows the complete snapshot JSON in a readable format, along with metadata such as the creation reason and timestamp.

**Section sources**
- [CVVersionResource.php](file://app/Filament/Resources/CVVersions/CVVersionResource.php#L18-L55)
- [CVVersionInfolist.php](file://app/Filament/Resources/CVVersions/Schemas/CVVersionInfolist.php#L7-L22)
- [CVVersionForm.php](file://app/Filament/Resources/CVVersions/Schemas/CVVersionForm.php#L9-L25)

## Use Cases
The CV versioning system supports several important use cases for managing CV evolution over time.

### Version Comparison
Users can compare different versions of a CV to track changes in content, structure, or formatting. By examining the `snapshot_json` field, users can identify what has been added, modified, or removed between versions.

### State Restoration
When a CV has been significantly modified and the user wishes to revert to a previous state, they can:
1. Navigate to the CV versions list
2. Select the desired historical version
3. Examine the snapshot JSON to verify it's the correct state
4. Use the data to manually restore the CV or create a new clone from the snapshot

### Historical Tracking
The system maintains a complete history of CV states, which is particularly valuable for:
- Documenting the evolution of a CV for different job applications
- Recovering content that was accidentally deleted
- Analyzing which CV versions led to interviews or job offers
- Maintaining a record of CV states before major revisions

The versioning system is read-only after creation, ensuring that historical snapshots remain immutable and reliable.

**Section sources**
- [quickstart.md](file://specs/002-roadmap-md/quickstart.md#L289-L294)
- [filament-resources.md](file://specs/002-roadmap-md/contracts/filament-resources.md#L111-L149)

## Performance Considerations
Storing complete JSON snapshots of CVs presents several performance considerations that must be addressed.

### Storage Impact
The `snapshot_json` field can become large, especially for CVs with extensive content. Each snapshot stores the complete state, including all sections, skills, experiences, and other components. This can lead to significant database growth over time, particularly for users who frequently clone or modify CVs.

### Query Performance
The system includes an index on the `cv_id` column to optimize queries that retrieve versions for a specific CV:

```php
$table->index('cv_id');
```

This ensures that fetching all versions of a particular CV remains performant even as the number of snapshots grows.

### Optimization Strategies
Potential optimization strategies include:
- Implementing a retention policy to automatically archive or remove old snapshots
- Compressing JSON data before storage
- Using database partitioning for the `cv_versions` table
- Implementing lazy loading for the `snapshot_json` field when not needed

The current implementation prioritizes data integrity and simplicity over storage efficiency, ensuring that complete and accurate snapshots are always available when needed.

**Section sources**
- [create_cv_versions_table.php](file://database/migrations/2025_10_04_002612_create_cv_versions_table.php#L0-L31)
- [CVVersion.php](file://app/Models/CVVersion.php#L7-L29)