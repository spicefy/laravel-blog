<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Depends on: posts
// tags and post_tag are created together because post_tag has no meaning without both

return new class extends Migration
{
    public function up(): void
    {
        // ── tags ─────────────────────────────────────────────────────────────
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // URL-safe, e.g. "kcse"
            $table->timestamps();
        });

        // ── post_tag (many-to-many pivot) ─────────────────────────────────────
        Schema::create('post_tag', function (Blueprint $table) {

            $table->foreignId('post_id')
                  ->constrained()       // → posts.id
                  ->cascadeOnDelete();

            $table->foreignId('tag_id')
                  ->constrained()       // → tags.id
                  ->cascadeOnDelete();

            // Composite PK prevents duplicate tag assignments
            $table->primary(['post_id', 'tag_id']);

            // Reverse index: quickly find all posts for a given tag
            // (MySQL uses the PK for post_id lookups; this covers tag_id → post_id)
            $table->index('tag_id');
        });
    }

    public function down(): void
    {
        // Drop pivot first to remove FK dependency on tags
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('tags');
    }
};
