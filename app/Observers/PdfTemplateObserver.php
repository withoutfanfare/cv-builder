<?php

namespace App\Observers;

use App\Models\PdfTemplate;

class PdfTemplateObserver
{
    /**
     * Handle the PdfTemplate "updating" event.
     */
    public function updating(PdfTemplate $pdfTemplate): void
    {
        // If setting this template as default, unset others
        if ($pdfTemplate->is_default && $pdfTemplate->isDirty('is_default')) {
            PdfTemplate::where('id', '!=', $pdfTemplate->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }

    /**
     * Handle the PdfTemplate "deleting" event.
     */
    public function deleting(PdfTemplate $pdfTemplate): void
    {
        // Prevent deletion of default template
        if ($pdfTemplate->is_default) {
            throw new \Exception('Cannot delete the default template. Please set another template as default first.');
        }
    }
}
