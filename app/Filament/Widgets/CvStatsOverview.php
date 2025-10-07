<?php

namespace App\Filament\Widgets;

use App\Models\Cv;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CvStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCvs = Cv::count();
        $cvsWithApplications = Cv::has('jobApplications')->count();
        $unusedCvs = $totalCvs - $cvsWithApplications;

        $mostUsedCv = Cv::withCount('jobApplications')
            ->orderBy('job_applications_count', 'desc')
            ->first();

        $mostUsedCvStat = $mostUsedCv
            ? "{$mostUsedCv->title} ({$mostUsedCv->job_applications_count} apps)"
            : 'N/A';

        return [
            Stat::make('Total CVs', $totalCvs)
                ->description('CVs created')
                ->descriptionIcon('heroicon-o-document-duplicate')
                ->color('primary'),

            Stat::make('CVs in Use', $cvsWithApplications)
                ->description('CVs with applications')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Unused CVs', $unusedCvs)
                ->description('CVs without applications')
                ->descriptionIcon('heroicon-o-document')
                ->color('gray'),

            Stat::make('Most Used CV', $mostUsedCvStat)
                ->description('Top performing CV')
                ->descriptionIcon('heroicon-o-star')
                ->color('warning'),
        ];
    }
}
