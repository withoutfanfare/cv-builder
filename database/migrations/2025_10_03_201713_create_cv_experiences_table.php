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
        Schema::create('cv_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_section_id')->constrained('cv_sections')->cascadeOnDelete();
            $table->string('job_title', 255);
            $table->string('company_name', 255);
            $table->string('location', 255)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->json('highlights');
            $table->unsignedInteger('display_order');
            $table->timestamps();

            $table->index(['cv_section_id', 'display_order']);
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_experiences');
    }
};
