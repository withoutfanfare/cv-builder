<?php

use App\Models\JobApplication;
use Carbon\Carbon;

describe('Needs Attention Filter', function () {
    test('needs attention includes overdue action date', function () {
        $overdueApp = JobApplication::factory()->create([
            'company_name' => 'Overdue Company',
            'next_action_date' => Carbon::yesterday(),
            'application_status' => 'pending',
        ]);

        $futureApp = JobApplication::factory()->create([
            'next_action_date' => Carbon::tomorrow(),
            'application_status' => 'pending',
            'send_status' => 'sent',
        ]);

        $needsAttention = JobApplication::needsAttention()->get();

        expect($needsAttention->contains($overdueApp))->toBeTrue()
            ->and($needsAttention->contains($futureApp))->toBeFalse();
    });

    test('needs attention includes draft send status', function () {
        $draftApp = JobApplication::factory()->create([
            'company_name' => 'Draft Company',
            'send_status' => 'draft',
            'application_status' => 'pending',
        ]);

        $sentApp = JobApplication::factory()->create([
            'send_status' => 'sent',
            'application_status' => 'pending',
            'next_action_date' => null,
        ]);

        $needsAttention = JobApplication::needsAttention()->get();

        expect($needsAttention->contains($draftApp))->toBeTrue();
    });

    test('needs attention includes pending interviewing', function () {
        $pendingApp = JobApplication::factory()->create([
            'application_status' => 'pending',
            'send_status' => 'sent',
        ]);

        $interviewingApp = JobApplication::factory()->create([
            'application_status' => 'interviewing',
            'send_status' => 'sent',
        ]);

        $needsAttention = JobApplication::needsAttention()->get();

        expect($needsAttention->contains($pendingApp))->toBeTrue()
            ->and($needsAttention->contains($interviewingApp))->toBeTrue();
    });

    test('needs attention excludes rejected withdrawn', function () {
        $rejectedApp = JobApplication::factory()->create([
            'company_name' => 'Rejected Company',
            'application_status' => 'rejected',
            'next_action_date' => Carbon::yesterday(), // Overdue but rejected
        ]);

        $withdrawnApp = JobApplication::factory()->create([
            'company_name' => 'Withdrawn Company',
            'application_status' => 'withdrawn',
            'send_status' => 'draft', // Draft but withdrawn
        ]);

        $needsAttention = JobApplication::needsAttention()->get();

        expect($needsAttention->contains($rejectedApp))->toBeFalse()
            ->and($needsAttention->contains($withdrawnApp))->toBeFalse();
    });
});
