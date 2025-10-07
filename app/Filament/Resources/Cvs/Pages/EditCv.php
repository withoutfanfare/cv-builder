<?php

namespace App\Filament\Resources\Cvs\Pages;

use App\Filament\Resources\Cvs\CvResource;
use App\Models\CvHeaderInfo;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCv extends EditRecord
{
    protected static string $resource = CvResource::class;

    public ?int $reviewId = null;

    public function mount($record): void
    {
        parent::mount($record);

        // Check for review query parameter
        $this->reviewId = request()->query('review');
    }

    public function getView(): string
    {
        // Use custom view only when review ID is present
        if ($this->reviewId) {
            return 'filament.resources.cvs.pages.edit-cv';
        }

        // Otherwise use parent's default view
        return parent::getView();
    }

    protected function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'reviewId' => $this->reviewId,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->form([
                    Select::make('profile_id')
                        ->label('Focus Profile (Optional)')
                        ->helperText('Select a profile to filter/reorder sections, or leave empty for full CV')
                        ->options(fn () => $this->record->sectionFocusProfiles()->pluck('name', 'id')->toArray())
                        ->placeholder('Full CV (All Sections)')
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    $url = $data['profile_id']
                        ? route('cv.pdf', ['cv' => $this->record->id, 'profile' => $data['profile_id']])
                        : route('cv.pdf', ['cv' => $this->record->id]);

                    return redirect($url);
                })
                ->color('primary'),
            Action::make('clone')
                ->label('Clone This CV')
                ->icon('heroicon-o-document-duplicate')
                ->requiresConfirmation()
                ->modalHeading('Clone CV')
                ->modalDescription('This will create a full copy of this CV with all sections, skills, and highlights. A version snapshot will be created for tracking.')
                ->action(function () {
                    $clonedCv = $this->record->cloneCv('Cloned via edit page');

                    Notification::make()
                        ->success()
                        ->title('CV cloned successfully')
                        ->body("Created '{$clonedCv->title}' with version snapshot")
                        ->send();

                    return redirect(CvResource::getUrl('edit', ['record' => $clonedCv]));
                })
                ->color('success'),
            DeleteAction::make()
                ->modalHeading('Archive CV')
                ->modalDescription('This CV will be archived. Job applications and PDF snapshots will remain accessible.'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load headerInfo data into the form
        if ($this->record->headerInfo) {
            $data['headerInfo'] = [
                'full_name' => $this->record->headerInfo->full_name,
                'job_title' => $this->record->headerInfo->job_title,
                'email' => $this->record->headerInfo->email,
                'phone' => $this->record->headerInfo->phone,
                'location' => $this->record->headerInfo->location,
                'linkedin_url' => $this->record->headerInfo->linkedin_url,
                'github_url' => $this->record->headerInfo->github_url,
                'website_url' => $this->record->headerInfo->website_url,
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract headerInfo data and save it separately
        if (isset($data['headerInfo'])) {
            $headerInfoData = $data['headerInfo'];

            CvHeaderInfo::updateOrCreate(
                ['cv_id' => $this->record->id],
                $headerInfoData
            );

            // Remove from main data to avoid errors
            unset($data['headerInfo']);
        }

        return $data;
    }
}
