<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;

class CvPdfController extends Controller
{
    public function download(Cv $cv, ?int $profile = null)
    {
        // Eager load all relationships including template
        $cv->load([
            'headerInfo',
            'sections.summary',
            'sections.skillCategories',
            'sections.experiences',
            'sections.projects',
            'sections.education',
            'sections.reference',
            'sections.customSection',
            'pdfTemplate',
        ]);

        // Get sections (optionally filtered/reordered by profile)
        $sections = $profile
            ? $cv->getSectionsWithProfile($profile)
            : $cv->sections()->with([
                'summary',
                'skillCategories',
                'experiences',
                'projects',
                'education',
                'reference',
                'customSection',
            ])->get();

        // Get skills with evidence count
        $skillsWithEvidence = $cv->getSkillsWithEvidence();

        // Get profile name for filename
        $profileName = null;
        if ($profile) {
            $profileModel = $cv->sectionFocusProfiles()->find($profile);
            $profileName = $profileModel ? Str::slug($profileModel->name) : null;
        }

        // Generate filename: cv-{slug}-{profile?}-{date}.pdf
        $slug = Str::slug($cv->title);
        $date = now()->format('Y-m-d');
        $filename = $profileName
            ? "cv-{$slug}-{$profileName}-{$date}.pdf"
            : "cv-{$slug}-{$date}.pdf";

        // Get template (selected or default)
        $template = $cv->template;

        // Generate and download PDF using selected template
        return Pdf::view($template->view_path, [
            'cv' => $cv,
            'sections' => $sections,
            'skillsWithEvidence' => $skillsWithEvidence,
            'profileName' => $profileName,
        ])
            ->format('a4')
            ->name($filename);
    }
}
