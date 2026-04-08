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
            // Comments default to unapproved; admin must approve before they appear
            $table->boolean('approved')->default(false);

            // ── Engagement ────────────────────────────────────────────────
            $table->unsignedInteger('likes')->default(0);

            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────────────
            // Most common query: load approved top-level comments for a post
            //   Comment::where('post_id', $id)->whereNull('parent_id')->where('approved', true)
            $table->index(['post_id', 'approved'], 'comments_post_approved_index');

            // Used when loading pending comments in admin moderation view
            $table->index('approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
