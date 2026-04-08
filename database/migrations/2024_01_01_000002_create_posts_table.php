<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Depends on: users, categories
// Must run BEFORE: tags (post_tag pivot), comments

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {

            $table->id();

            // ── Core content ──────────────────────────────────────────────
            $table->string('title');
            $table->string('slug')->unique();   // URL-safe, e.g. "kcse-2025-what-changed"
            $table->text('excerpt')->nullable(); // 1–2 sentence summary; used in cards + fallback meta
            $table->longText('content');         // Full HTML body (from rich-text editor)
            $table->string('featured_image')->nullable(); // relative path e.g. "images/posts/foo.jpg"

            // ── Relationships ─────────────────────────────────────────────
            $table->foreignId('category_id')
                  ->constrained()          // → categories.id
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained()          // → users.id  (author)
                  ->cascadeOnDelete();

            // ── Publishing ────────────────────────────────────────────────
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable(); // null = not yet scheduled

            // ── SEO fields ────────────────────────────────────────────────
            // All nullable — models fall back to title / excerpt / content
            $table->string('meta_title',       70)->nullable();  // <title> tag
            $table->string('meta_description', 160)->nullable(); // <meta name="description">
            $table->string('meta_keywords',    255)->nullable(); // JSON-LD + Bing

            // ── Performance helpers ───────────────────────────────────────
            // reading_time is auto-computed by PostObserver on every save
            $table->unsignedSmallInteger('reading_time')->default(1); // minutes
            $table->unsignedInteger('view_count')->default(0);

            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────────────
            // Composite index covers the most common query:
            //   Post::published()->latest()  →  WHERE status = 'published' AND published_at <= now() ORDER BY published_at DESC
            $table->index(['status', 'published_at'], 'posts_status_published_at_index');

            // Individual FK indexes (MySQL adds these automatically for FK columns,
            // but explicit declarations make intent clear and work on all drivers)
            $table->index('category_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
