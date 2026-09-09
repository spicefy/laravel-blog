<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Depends on: posts
// Self-referencing FK (parent_id → comments.id) enables unlimited thread depth

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {

            $table->id();

            // ── Relationships ─────────────────────────────────────────────
            $table->foreignId('post_id')
                  ->constrained()       // → posts.id
                  ->cascadeOnDelete();  // deleting a post removes all its comments

            // Self-referencing: null = top-level comment, set = reply to another comment
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('comments') // → comments.id
                  ->cascadeOnDelete();       // deleting a comment removes its replies too

            // ── Author (guest — no user account required) ─────────────────
            $table->string('name');
            $table->string('email')->nullable(); // stored but never displayed publicly

            // ── Body ─────────────────────────────────────────────────────
            $table->text('comment');

            // ── Moderation ────────────────────────────────────────────────
            $table->boolean('approved')->default(false);
            $table->string('ip_address', 45)->nullable(); // spam/abuse tracking

            // ── Engagement ────────────────────────────────────────────────
            $table->unsignedInteger('likes')->default(0);

            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────────────
            $table->index(['post_id', 'approved'], 'comments_post_approved_index');
            $table->index('approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};