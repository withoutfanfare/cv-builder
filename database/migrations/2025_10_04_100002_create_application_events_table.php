<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('application_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->cascadeOnDelete();
            $table->enum('event_type', [
                'submitted',
                'reply_received',
                'interview_scheduled',
                'interview_completed',
                'offer_received',
                'rejected',
                'withdrawn',
            ]);
            $table->timestamp('occurred_at');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('job_application_id');
            $table->index('event_type');
            $table->index(['job_application_id', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_events');
    }
};
