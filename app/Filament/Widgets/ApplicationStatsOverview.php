<?php

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApplicationStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalApplications = JobApplication::count();
        $sentApplications = JobApplication::where('send_status', 'sent')->count();
        $interviewingCount = JobApplication::where('application_status', 'interviewing')->count();
        $offeredCount = JobApplication::where('application_status', 'offered')->count();
        $rejectedCount = JobApplication::where('application_status', 'rejected')->count();
        $pendingCount = JobApplication::whereIn('application_status', ['pending', 'reviewed'])->count();

        return [
            Stat::make('Total Applications', $totalApplications)
                ->description('All job applications')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Sent Applications', $sentApplications)
                ->description('Successfully submitted')
                ->descriptionIcon('heroicon-o-paper-airplane')
                ->color('success'),

            Stat::make('Interviewing', $interviewingCount)
                ->description('Currently in interview stage')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            Stat::make('Offers', $offeredCount)
                ->description('Job offers received')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('warning'),

            Stat::make('Pending Response', $pendingCount)
                ->description('Awaiting feedback')
                ->descriptionIcon('heroicon-o-clock')
                ->color('gray'),

            Stat::make('Rejected', $rejectedCount)
                ->description('Applications not successful')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
