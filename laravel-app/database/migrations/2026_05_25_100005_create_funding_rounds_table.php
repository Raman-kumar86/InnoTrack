<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('funding_rounds')) {
            Schema::create('funding_rounds', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('startup_id')->constrained()->cascadeOnDelete();
                $table->string('round_type');
                $table->unsignedBigInteger('amount_raised_usd');
                $table->foreignId('investor_id')->nullable()->constrained();
                $table->date('funding_date');
                $table->date('expected_close_date')->nullable();
                $table->string('round_status')->default('Completed');
                $table->float('equity_diluted_percent')->nullable();
                $table->unsignedBigInteger('valuation_after_round_usd');
                $table->unsignedBigInteger('pre_money_valuation_usd')->nullable();
                $table->string('lead_investor')->default('Yes');
                $table->string('currency')->default('USD');
                $table->float('exchange_rate_to_usd')->default(1);
                $table->float('interest_rate')->nullable();
                $table->string('grant_authority')->nullable();
                $table->float('conversion_cap')->nullable();
                $table->float('discount_rate')->nullable();
                $table->boolean('is_publicly_announced')->default(false);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_rounds');
    }
};
