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
        Schema::create('pdf_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('cv_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cv_version_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_path', 500)->unique();
            $table->string('hash', 64);
            $table->timestamp('created_at');

            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_snapshots');
    }
};
