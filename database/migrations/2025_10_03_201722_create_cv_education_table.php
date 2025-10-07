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
        Schema::create('cv_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_section_id')->constrained('cv_sections')->cascadeOnDelete();
            $table->string('degree', 255);
            $table->string('institution', 255);
            $table->unsignedInteger('start_year');
            $table->unsignedInteger('end_year')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('display_order');
            $table->timestamps();

            $table->index(['cv_section_id', 'display_order']);
            $table->index('start_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_education');
    }
};
