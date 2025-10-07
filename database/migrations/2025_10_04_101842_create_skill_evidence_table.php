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
        Schema::create('skill_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->constrained('cvs')->cascadeOnDelete();
            $table->string('skill_name');
            $table->string('evidenceable_type');
            $table->unsignedBigInteger('evidenceable_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('cv_id');
            $table->index('skill_name');
            $table->index(['evidenceable_type', 'evidenceable_id']);
            $table->unique(['cv_id', 'skill_name', 'evidenceable_type', 'evidenceable_id'], 'skill_evidence_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_evidence');
    }
};
