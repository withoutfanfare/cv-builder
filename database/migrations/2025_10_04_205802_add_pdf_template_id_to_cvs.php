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
        Schema::table('cvs', function (Blueprint $table) {
            $table->foreignId('pdf_template_id')
                ->nullable()
                ->after('title')
                ->constrained('pdf_templates')
                ->nullOnDelete();

            $table->index('pdf_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            $table->dropForeign(['pdf_template_id']);
            $table->dropIndex(['pdf_template_id']);
            $table->dropColumn('pdf_template_id');
        });
    }
};
