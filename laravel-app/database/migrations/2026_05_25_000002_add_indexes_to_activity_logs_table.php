<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'created_at']);
            $table->index(['result', 'created_at']);
            $table->index('created_at');
            $table->index('is_system');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['module', 'created_at']);
            $table->dropIndex(['result', 'created_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_system']);
        });
    }
};