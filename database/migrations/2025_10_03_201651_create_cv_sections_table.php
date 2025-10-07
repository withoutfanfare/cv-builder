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
        Schema::create('cv_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->constrained('cvs')->cascadeOnDelete();
            $table->enum('section_type', ['header', 'summary', 'skills', 'experience', 'projects', 'education', 'references', 'custom']);
            $table->unsignedInteger('display_order');
            $table->timestamps();

            $table->index(['cv_id', 'display_order']);
            $table->unique(['cv_id', 'section_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_sections');
    }
};
