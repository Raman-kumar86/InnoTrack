<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('activity_logs', 'causer_id')) {
                $table->foreignId('causer_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('activity_logs', 'target_user_id')) {
                $table->foreignId('target_user_id')->nullable()->after('causer_id')->constrained('users')->nullOnDelete();
            }

            $table->index(['causer_id', 'created_at']);
            $table->index(['target_user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->dropIndex(['causer_id', 'created_at']);
            $table->dropIndex(['target_user_id', 'created_at']);
            $table->dropIndex(['action', 'created_at']);

            if (Schema::hasColumn('activity_logs', 'target_user_id')) {
                $table->dropConstrainedForeignId('target_user_id');
            }

            if (Schema::hasColumn('activity_logs', 'causer_id')) {
                $table->dropConstrainedForeignId('causer_id');
            }
        });
    }
};
