<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('founders')) {
            Schema::create('founders', function (Blueprint $table): void {
                $table->id();
                $table->string('full_name');
                $table->string('gender', 20)->nullable();
                $table->unsignedSmallInteger('age')->nullable();
                $table->string('email')->nullable()->index();
                $table->string('phone', 30)->nullable();
                $table->string('education')->nullable();
                $table->string('college')->nullable();
                $table->enum('iit_iim_nit', ['Yes', 'No'])->nullable();
                $table->enum('serial_entrepreneur', ['Yes', 'No'])->nullable();
                $table->string('linkedin_profile')->nullable();
                $table->unsignedSmallInteger('experience_years')->nullable();
                $table->string('prev_company')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table): void {
                $table->id();
                $table->string('state_name');
                $table->string('state_code', 10)->nullable()->index();
                $table->string('region', 50)->nullable();
                $table->enum('startup_hub', ['Yes', 'No'])->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sectors')) {
            Schema::create('sectors', function (Blueprint $table): void {
                $table->id();
                $table->string('sector_name');
                $table->text('description')->nullable();
                $table->enum('is_priority_sector', ['Yes', 'No'])->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('incubators')) {
            Schema::create('incubators', function (Blueprint $table): void {
                $table->id();
                $table->string('incubator_name');
                $table->string('city')->nullable();
                $table->unsignedBigInteger('state_id')->nullable()->index();
                $table->string('incubator_type')->nullable();
                $table->unsignedInteger('total_startups_supported')->nullable();
                $table->unsignedInteger('active_startups')->nullable();
                $table->decimal('funding_provided_crore', 14, 2)->nullable();
                $table->unsignedSmallInteger('established_year')->nullable();
                $table->string('website')->nullable();
                $table->string('email')->nullable();
                $table->string('phone', 30)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('investors')) {
            Schema::create('investors', function (Blueprint $table): void {
                $table->id();
                $table->string('investor_name');
                $table->string('investor_type')->nullable();
                $table->string('city')->nullable();
                $table->unsignedBigInteger('state_id')->nullable()->index();
                $table->decimal('aum_crore', 14, 2)->nullable();
                $table->unsignedInteger('portfolio_count')->nullable();
                $table->string('website')->nullable();
                $table->string('email')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('startups')) {
            Schema::create('startups', function (Blueprint $table): void {
                $table->id();
                $table->string('startup_name');
                $table->string('slug')->unique();
                $table->enum('dpiit_recognized', ['Yes', 'No'])->nullable();
                $table->string('registration_number')->nullable();
                $table->unsignedSmallInteger('founded_year')->nullable();
                $table->unsignedSmallInteger('founder_count')->nullable();
                $table->unsignedBigInteger('sector_id')->nullable()->index();
                $table->unsignedBigInteger('state_id')->nullable()->index();
                $table->string('city')->nullable();
                $table->string('funding_stage')->nullable();
                $table->decimal('total_funding_usd', 20, 2)->nullable();
                $table->decimal('valuation_usd', 20, 2)->nullable();
                $table->decimal('annual_revenue_inr', 20, 2)->nullable();
                $table->unsignedInteger('employee_count')->nullable();
                $table->enum('women_led', ['Yes', 'No'])->nullable();
                $table->enum('sustainability_focus', ['Yes', 'No'])->nullable();
                $table->enum('ai_enabled', ['Yes', 'No'])->nullable();
                $table->enum('export_business', ['Yes', 'No'])->nullable();
                $table->string('website')->nullable();
                $table->string('email')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('linkedin_url')->nullable();
                $table->string('status')->nullable();
                $table->date('last_funding_date')->nullable();
                $table->decimal('growth_percentage', 10, 2)->nullable();
                $table->unsignedInteger('jobs_created')->nullable();
                $table->unsignedInteger('patents_filed')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('funding_rounds')) {
            Schema::create('funding_rounds', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('startup_id')->nullable()->index();
                $table->string('round_type')->nullable();
                $table->decimal('amount_raised_usd', 20, 2)->nullable();
                $table->unsignedBigInteger('investor_id')->nullable()->index();
                $table->date('funding_date')->nullable();
                $table->decimal('equity_diluted_percent', 8, 2)->nullable();
                $table->decimal('valuation_after_round_usd', 20, 2)->nullable();
                $table->enum('lead_investor', ['Yes', 'No'])->nullable();
                $table->string('currency', 10)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('startup_documents')) {
            Schema::create('startup_documents', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('startup_id')->nullable()->index();
                $table->string('document_type')->nullable();
                $table->string('document_name')->nullable();
                $table->string('file_path')->nullable();
                $table->unsignedInteger('file_size_kb')->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable()->index();
                $table->string('status')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('startup_incubator_map')) {
            Schema::create('startup_incubator_map', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('startup_id')->nullable()->index();
                $table->unsignedBigInteger('incubator_id')->nullable()->index();
                $table->date('joining_date')->nullable();
                $table->date('graduation_date')->nullable();
                $table->decimal('grant_received_inr', 20, 2)->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('startup_investors')) {
            Schema::create('startup_investors', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('startup_id')->nullable()->index();
                $table->unsignedBigInteger('investor_id')->nullable()->index();
                $table->decimal('investment_amount_usd', 20, 2)->nullable();
                $table->date('investment_date')->nullable();
                $table->decimal('equity_stake_percent', 8, 2)->nullable();
                $table->string('investment_stage')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('startup_metrics')) {
            Schema::create('startup_metrics', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('startup_id')->nullable()->index();
                $table->string('metric_month', 7)->nullable();
                $table->decimal('revenue_inr', 20, 2)->nullable();
                $table->unsignedBigInteger('active_users')->nullable();
                $table->decimal('churn_rate_percent', 8, 2)->nullable();
                $table->decimal('burn_rate_inr', 20, 2)->nullable();
                $table->decimal('profit_margin_percent', 8, 2)->nullable();
                $table->unsignedBigInteger('app_downloads')->nullable();
                $table->decimal('monthly_growth_percent', 8, 2)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('startup_tags')) {
            Schema::create('startup_tags', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('startup_id')->nullable()->index();
                $table->string('tag')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('startup_updates')) {
            Schema::create('startup_updates', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('startup_id')->nullable()->index();
                $table->string('update_type')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->date('update_date')->nullable();
                $table->boolean('is_published')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('startup_id')->nullable()->index();
                $table->string('notification_type')->nullable();
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->boolean('is_read')->nullable();
                $table->string('priority', 20)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('startup_updates');
        Schema::dropIfExists('startup_tags');
        Schema::dropIfExists('startup_metrics');
        Schema::dropIfExists('startup_investors');
        Schema::dropIfExists('startup_incubator_map');
        Schema::dropIfExists('startup_documents');
        Schema::dropIfExists('funding_rounds');
        Schema::dropIfExists('startups');
        Schema::dropIfExists('investors');
        Schema::dropIfExists('incubators');
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('states');
        Schema::dropIfExists('founders');
    }
};
