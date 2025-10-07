<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\PDFSnapshot;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class PdfSnapshotService
{
    /**
     * Create a PDF snapshot for a job application
     *
     * @throws \Exception
     */
    public function create(JobApplication $jobApplication): PDFSnapshot
    {
        // Ensure CV exists
        if (! $jobApplication->cv) {
            throw new \Exception('Job application must have a CV before creating PDF snapshot');
        }

        // Load CV with all relationships for complete rendering
        $cv = $jobApplication->cv->load([
            'headerInfo',
            'sections',
            'sections.summary',
            'sections.skillCategories',
            'sections.experiences',
            'sections.projects',
            'sections.education',
            'sections.customSections',
        ]);

        // Generate PDF
        $pdf = Pdf::view('cv.pdf', ['cv' => $cv])
            ->format('a4')
            ->name('cv.pdf');

        // Get PDF content as binary (base64 decode the output)
        $pdfContent = base64_decode($pdf->base64());

        // Validate PDF size (max 10MB to prevent storage exhaustion)
        $maxSizeBytes = 10 * 1024 * 1024; // 10MB
        if (strlen($pdfContent) > $maxSizeBytes) {
            throw new \Exception('PDF exceeds maximum size of 10MB. Please reduce CV content.');
        }

        // Calculate SHA-256 hash
        $hash = hash('sha256', $pdfContent);

        // Define storage path with sanitized ID
        $sanitizedId = (int) $jobApplication->id;
        $filePath = sprintf('pdf-snapshots/%d_%s.pdf', $sanitizedId, $hash);

        // Store PDF file
        Storage::disk('local')->put($filePath, $pdfContent);

        // Create PDFSnapshot record
        $snapshot = PDFSnapshot::create([
            'job_application_id' => $jobApplication->id,
            'cv_id' => $cv->id,
            'cv_version_id' => null, // Can be linked to version if needed
            'file_path' => $filePath,
            'hash' => $hash,
            'created_at' => now(),
        ]);

        return $snapshot;
    }
}
