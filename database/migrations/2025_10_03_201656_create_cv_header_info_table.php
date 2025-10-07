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
        Schema::create('cv_header_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->unique()->constrained('cvs')->cascadeOnDelete();
            $table->string('full_name', 255);
            $table->string('job_title', 255);
            $table->string('phone', 50)->nullable();
            $table->string('email', 255);
            $table->string('location', 255)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('github_url', 500)->nullable();
            $table->string('website_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_header_info');
    }
};
