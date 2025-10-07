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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('company_website')->nullable();
            $table->text('company_notes')->nullable();
            $table->string('point_of_contact_name')->nullable();
            $table->string('point_of_contact_email')->nullable();
            $table->enum('send_status', ['draft', 'sent'])->default('draft');
            $table->enum('application_status', [
                'pending',
                'reviewed',
                'interviewing',
                'offered',
                'rejected',
                'accepted',
                'withdrawn',
            ])->default('pending');
            $table->json('interview_dates')->nullable();
            $table->text('interview_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('cv_id');
            $table->index('application_status');
            $table->index('send_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
