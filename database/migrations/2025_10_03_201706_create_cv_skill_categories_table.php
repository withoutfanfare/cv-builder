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
        Schema::create('cv_skill_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_section_id')->constrained('cv_sections')->cascadeOnDelete();
            $table->string('category_name', 255);
            $table->json('skills');
            $table->unsignedInteger('display_order');
            $table->timestamps();

            $table->index(['cv_section_id', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_skill_categories');
    }
};
