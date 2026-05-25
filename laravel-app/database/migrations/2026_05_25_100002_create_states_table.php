<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table): void {
                $table->id();
                $table->string('state_name');
                $table->string('state_code', 5);
                $table->string('region')->nullable();
                $table->string('startup_hub')->default('No');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
