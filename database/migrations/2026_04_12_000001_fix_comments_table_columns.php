<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {

            // Add 'approved' if it doesn't exist yet
            if (!Schema::hasColumn('comments', 'approved')) {
                $table->boolean('approved')
                      ->default(false)
                      ->after('comment');
            }

            // Add 'likes' if it doesn't exist yet
            if (!Schema::hasColumn('comments', 'likes')) {
                $table->unsignedInteger('likes')
                      ->default(0)
                      ->after('approved');
            }

            // Add 'parent_id' if it doesn't exist yet
            if (!Schema::hasColumn('comments', 'parent_id')) {
                $table->foreignId('parent_id')
                      ->nullable()
                      ->after('post_id')
                      ->constrained('comments')
                      ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['approved', 'likes']);
        });
    }
};
