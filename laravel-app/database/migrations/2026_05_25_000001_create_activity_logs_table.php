<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->string('user_name')->nullable();
            $table->string('module');
            $table->string('action');
            $table->string('result')->default('Success');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->morphs('loggable');
            $table->json('metadata')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_system')->default(false);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};