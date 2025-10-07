<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cv_sections', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique(['cv_id', 'section_type']);

            // Add title field for custom sections
            $table->string('title')->nullable()->after('section_type');
        });

        // Update the enum to include 'custom' - requires rebuilding for SQLite
        DB::statement("UPDATE cv_sections SET section_type = 'custom' WHERE section_type NOT IN ('header', 'summary', 'skills', 'experience', 'projects', 'education', 'references')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cv_sections', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->unique(['cv_id', 'section_type']);
        });
    }
};
