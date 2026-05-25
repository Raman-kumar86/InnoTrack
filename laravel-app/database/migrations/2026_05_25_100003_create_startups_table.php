<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('startups')) {
            Schema::create('startups', function (Blueprint $table): void {
                $table->id();
                $table->string('startup_name');
                $table->string('slug')->unique();
                $table->string('dpiit_recognized')->default('No');
                $table->string('registration_number')->unique();
                $table->unsignedSmallInteger('founded_year');
                $table->unsignedTinyInteger('founder_count')->default(1);
                $table->foreignId('sector_id')->constrained();
                $table->foreignId('state_id')->constrained();
                $table->string('city');
                $table->string('funding_stage')->nullable();
                $table->unsignedBigInteger('total_funding_usd')->default(0);
                $table->unsignedBigInteger('valuation_usd')->default(0);
                $table->unsignedBigInteger('annual_revenue_inr')->default(0);
                $table->unsignedInteger('employee_count')->default(0);
                $table->string('women_led')->default('No');
                $table->string('sustainability_focus')->default('No');
                $table->string('ai_enabled')->default('No');
                $table->string('export_business')->default('No');
                $table->string('website')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->string('status')->default('Active');
                $table->date('last_funding_date')->nullable();
                $table->float('growth_percentage')->default(0);
                $table->unsignedInteger('jobs_created')->default(0);
                $table->unsignedInteger('patents_filed')->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('startups');
    }
};
