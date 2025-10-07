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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->timestamp('ai_review_requested_at')->nullable()
                ->comment('When user triggered CV review');

            $table->timestamp('ai_review_completed_at')->nullable()
                ->comment('When review processing finished');

            $table->integer('ai_review_cost_cents')->nullable()
                ->comment('OpenAI API cost in cents for this review');

            $table->json('ai_review_data')->nullable()
                ->comment('Complete review results as JSON');

            $table->index('ai_review_completed_at');
            $table->index(['send_status', 'ai_review_completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['ai_review_completed_at']);
            $table->dropIndex(['send_status', 'ai_review_completed_at']);
            $table->dropColumn([
                'ai_review_requested_at',
                'ai_review_completed_at',
                'ai_review_cost_cents',
                'ai_review_data',
            ]);
        });
    }
};
