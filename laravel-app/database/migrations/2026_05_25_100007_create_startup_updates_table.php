<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('startup_updates')) {
            Schema::create('startup_updates', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('startup_id')->constrained();
                $table->string('update_type');
                $table->string('title');
                $table->text('description')->nullable();
                $table->date('update_date');
                $table->boolean('is_published')->default(false);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('startup_updates');
    }
};
