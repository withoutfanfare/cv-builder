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
            $table->string('job_title')->nullable();
            $table->string('source', 100)->nullable();
            $table->date('application_deadline')->nullable();
            $table->date('next_action_date')->nullable();
            $table->text('job_description')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            // Indexes for query performance
            $table->index('next_action_date');
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['next_action_date']);
            $table->dropIndex(['last_activity_at']);

            $table->dropColumn([
                'job_title',
                'source',
                'application_deadline',
                'next_action_date',
                'job_description',
                'last_activity_at',
            ]);
        });
    }
};
