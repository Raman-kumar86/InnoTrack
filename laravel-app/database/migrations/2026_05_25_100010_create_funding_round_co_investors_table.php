<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('funding_round_co_investors')) {
            Schema::create('funding_round_co_investors', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('funding_round_id')->constrained()->cascadeOnDelete();
                $table->foreignId('investor_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['funding_round_id', 'investor_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_round_co_investors');
    }
};
