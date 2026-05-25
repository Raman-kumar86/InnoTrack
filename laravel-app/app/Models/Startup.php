<?php

namespace App\Models;

use App\Models\Founder;
use App\Models\FundingRound;
use App\Models\Sector;
use App\Models\StartupDocument;
use App\Models\StartupTag;
use App\Models\StartupUpdate;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Startup extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_name', 'slug', 'dpiit_recognized',
        'registration_number', 'founded_year', 'founder_count',
        'sector_id', 'state_id', 'city', 'funding_stage',
        'total_funding_usd', 'valuation_usd',
        'annual_revenue_inr', 'employee_count', 'women_led',
        'sustainability_focus', 'ai_enabled', 'export_business',
        'website', 'email', 'phone', 'linkedin_url', 'status',
        'last_funding_date', 'growth_percentage', 'jobs_created',
        'patents_filed', 'description',
    ];

    protected $casts = [
        'dpiit_recognized' => 'boolean',
        'women_led' => 'boolean',
        'ai_enabled' => 'boolean',
        'sustainability_focus' => 'boolean',
        'export_business' => 'boolean',
        'growth_percentage' => 'float',
        'last_funding_date' => 'date',
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function founders()
    {
        return $this->hasMany(Founder::class);
    }

    public function fundingRounds()
    {
        return $this->hasMany(FundingRound::class)
            ->orderBy('funding_date', 'desc');
    }

    public function investors()
    {
        return $this->hasMany(StartupInvestor::class);
    }

    public function documents()
    {
        return $this->hasMany(StartupDocument::class);
    }

    public function tags()
    {
        return $this->hasMany(StartupTag::class);
    }

    public function updates()
    {
        return $this->hasMany(StartupUpdate::class);
    }

    public function recalculateFundingTotals(): void
    {
        $this->total_funding_usd = $this->fundingRounds()->sum('amount_raised_usd');
        $this->save();
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('startup_name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhereHas('sector', fn ($query) => $query->where('sector_name', 'like', "%{$search}%"))
                        ->orWhereHas('founders', fn ($query) => $query->where('full_name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['sector_id'] ?? null, fn ($query, $value) => $query->where('sector_id', $value))
            ->when($filters['state_id'] ?? null, fn ($query, $value) => $query->where('state_id', $value))
            ->when($filters['funding_stage'] ?? null, fn ($query, $value) => $query->whereIn('funding_stage', (array) $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['dpiit_recognized'] ?? null, fn ($query, $value) => $query->where('dpiit_recognized', $value === 'Yes'))
            ->when($filters['women_led'] ?? null, fn ($query) => $query->where('women_led', true))
            ->when($filters['ai_enabled'] ?? null, fn ($query) => $query->where('ai_enabled', true))
            ->when($filters['year_from'] ?? null, fn ($query, $value) => $query->where('founded_year', '>=', $value))
            ->when($filters['year_to'] ?? null, fn ($query, $value) => $query->where('founded_year', '<=', $value))
            ->when($filters['funding_min'] ?? null, fn ($query, $value) => $query->where('total_funding_usd', '>=', $value))
            ->when($filters['funding_max'] ?? null, fn ($query, $value) => $query->where('total_funding_usd', '<=', $value));
    }

    public function scopeSorted($query, $sort)
    {
        return match ($sort) {
            'newest' => $query->orderByDesc('created_at'),
            'oldest' => $query->orderBy('created_at'),
            'name_asc' => $query->orderBy('startup_name'),
            'name_desc' => $query->orderByDesc('startup_name'),
            'status_asc' => $query->orderBy('status'),
            'status_desc' => $query->orderByDesc('status'),
            'founded_new' => $query->orderByDesc('founded_year'),
            'founded_old' => $query->orderBy('founded_year'),
            'funding_high' => $query->orderByDesc('total_funding_usd'),
            'funding_low' => $query->orderBy('total_funding_usd'),
            'growth_high' => $query->orderByDesc('growth_percentage'),
            'growth_low' => $query->orderBy('growth_percentage'),
            'employees_high' => $query->orderByDesc('employee_count'),
            'valuation_high' => $query->orderByDesc('valuation_usd'),
            default => $query->orderByDesc('created_at'),
        };
    }
}