<?php

use App\Filament\Widgets\ApplicationStatsOverview;
use App\Filament\Widgets\ApplicationStatusChart;
use App\Filament\Widgets\CvStatsOverview;
use App\Filament\Widgets\RecentApplicationsTable;
use App\Models\Cv;
use App\Models\JobApplication;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('can render application stats overview widget', function () {
    $cv = Cv::factory()->create();
    JobApplication::factory()->count(5)->create(['cv_id' => $cv->id]);

    Livewire::test(ApplicationStatsOverview::class)
        ->assertOk();
});

it('can render cv stats overview widget', function () {
    Cv::factory()->count(3)->create();

    Livewire::test(CvStatsOverview::class)
        ->assertOk();
});

it('can render application status chart widget', function () {
    $cv = Cv::factory()->create();
    JobApplication::factory()->count(3)->create(['cv_id' => $cv->id]);

    Livewire::test(ApplicationStatusChart::class)
        ->assertOk();
});

it('can render recent applications table widget', function () {
    $cv = Cv::factory()->create();
    JobApplication::factory()->count(5)->create(['cv_id' => $cv->id]);

    Livewire::test(RecentApplicationsTable::class)
        ->assertOk();
});

it('displays correct application counts in stats', function () {
    $cv = Cv::factory()->create();
    JobApplication::factory()->create(['cv_id' => $cv->id, 'send_status' => 'sent', 'application_status' => 'interviewing']);
    JobApplication::factory()->create(['cv_id' => $cv->id, 'send_status' => 'draft', 'application_status' => 'pending']);
    JobApplication::factory()->create(['cv_id' => $cv->id, 'send_status' => 'sent', 'application_status' => 'offered']);

    Livewire::test(ApplicationStatsOverview::class)
        ->assertSee('Total Applications')
        ->assertSee('3')
        ->assertOk();
});

it('displays correct cv counts in stats', function () {
    $cv1 = Cv::factory()->create(['title' => 'Senior Developer CV']);
    $cv2 = Cv::factory()->create(['title' => 'Junior Developer CV']);
    JobApplication::factory()->count(2)->create(['cv_id' => $cv1->id]);

    Livewire::test(CvStatsOverview::class)
        ->assertSee('Total CVs')
        ->assertSee('2')
        ->assertOk();
});
