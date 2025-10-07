<?php

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ApplicationStatusChart extends ChartWidget
{
    protected ?string $heading = 'Application Status Breakdown';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statusCounts = JobApplication::select('application_status', DB::raw('count(*) as count'))
            ->groupBy('application_status')
            ->pluck('count', 'application_status')
            ->toArray();

        $labels = [
            'pending' => 'Pending',
            'reviewed' => 'Reviewed',
            'interviewing' => 'Interviewing',
            'offered' => 'Offered',
            'rejected' => 'Rejected',
            'accepted' => 'Accepted',
            'withdrawn' => 'Withdrawn',
        ];

        $data = [];
        $chartLabels = [];

        foreach ($labels as $key => $label) {
            if (isset($statusCounts[$key]) && $statusCounts[$key] > 0) {
                $chartLabels[] = $label;
                $data[] = $statusCounts[$key];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Applications',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(156, 163, 175)', // gray - pending
                        'rgb(59, 130, 246)',  // blue - reviewed
                        'rgb(251, 191, 36)',  // yellow - interviewing
                        'rgb(34, 197, 94)',   // green - offered
                        'rgb(239, 68, 68)',   // red - rejected
                        'rgb(168, 85, 247)',  // purple - accepted
                        'rgb(107, 114, 128)', // gray-dark - withdrawn
                    ],
                ],
            ],
            'labels' => $chartLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
