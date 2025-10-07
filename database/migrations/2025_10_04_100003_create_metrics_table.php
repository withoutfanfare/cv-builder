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
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->enum('metric_type', [
                'applications_per_week',
                'response_rate',
                'interview_conversion_rate',
                'offer_rate',
                'median_days_to_first_response',
            ]);
            $table->decimal('value', 10, 2);
            $table->date('time_period_start');
            $table->date('time_period_end');
            $table->timestamp('last_refreshed_at');
            $table->timestamps();

            // Indexes
            $table->index('metric_type');
            $table->index(['time_period_start', 'time_period_end']);
            $table->unique(['metric_type', 'time_period_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
