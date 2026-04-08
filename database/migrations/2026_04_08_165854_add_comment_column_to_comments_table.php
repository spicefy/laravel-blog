<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Add comment column if it doesn't exist
            if (!Schema::hasColumn('comments', 'comment')) {
                $table->text('comment')->after('email');
            }
            
            // Add other missing columns
            if (!Schema::hasColumn('comments', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('comment');
            }
            
            if (!Schema::hasColumn('comments', 'likes')) {
                $table->unsignedInteger('likes')->default(0)->after('is_approved');
            }
            
            if (!Schema::hasColumn('comments', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('likes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['comment', 'is_approved', 'likes', 'ip_address']);
        });
    }
};