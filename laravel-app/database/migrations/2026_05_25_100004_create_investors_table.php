<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('investors')) {
            Schema::create('investors', function (Blueprint $table): void {
                $table->id();
                $table->string('investor_name');
                $table->string('investor_type');
                $table->string('city')->nullable();
                $table->foreignId('state_id')->nullable()->constrained();
                $table->unsignedBigInteger('aum_crore')->default(0);
                $table->unsignedInteger('portfolio_count')->default(0);
                $table->string('website')->nullable();
                $table->string('email')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
