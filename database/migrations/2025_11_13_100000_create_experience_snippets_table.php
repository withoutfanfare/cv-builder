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
        Schema::create('experience_snippets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('category')->nullable(); // e.g., "leadership", "technical", "project management"
            $table->json('tags')->nullable(); // Skills, keywords for matching
            $table->string('role_type')->nullable(); // e.g., "engineer", "manager", "designer"
            $table->string('industry')->nullable(); // e.g., "tech", "finance", "healthcare"
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('role_type');
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_snippets');
    }
};
