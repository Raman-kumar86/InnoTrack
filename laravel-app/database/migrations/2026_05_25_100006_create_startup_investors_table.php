<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('startup_investors')) {
            Schema::create('startup_investors', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('startup_id')->constrained();
                $table->foreignId('investor_id')->constrained();
                $table->unsignedBigInteger('investment_amount_usd');
                $table->date('investment_date');
                $table->float('equity_stake_percent')->nullable();
                $table->string('investment_stage')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('startup_investors');
    }
};
