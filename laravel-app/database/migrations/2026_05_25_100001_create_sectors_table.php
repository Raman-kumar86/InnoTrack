<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('sectors')) {
            Schema::create('sectors', function (Blueprint $table): void {
                $table->id();
                $table->string('sector_name');
                $table->string('description')->nullable();
                $table->string('is_priority_sector')->default('No');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sectors');
    }
};
