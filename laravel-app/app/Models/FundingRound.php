<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_id', 'round_type', 'amount_raised_usd',
        'investor_id', 'funding_date', 'expected_close_date',
        'round_status', 'equity_diluted_percent',
        'valuation_after_round_usd', 'pre_money_valuation_usd',
        'lead_investor', 'currency', 'exchange_rate_to_usd',
        'interest_rate', 'grant_authority',
        'conversion_cap', 'discount_rate',
        'is_publicly_announced', 'notes', 'created_by',
    ];

    protected $casts = [
        'funding_date' => 'date',
        'expected_close_date' => 'date',
        'is_publicly_announced' => 'boolean',
        'equity_diluted_percent' => 'float',
        'exchange_rate_to_usd' => 'float',
        'interest_rate' => 'float',
        'conversion_cap' => 'float',
        'discount_rate' => 'float',
    ];

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function coInvestors()
    {
        return $this->belongsToMany(
            Investor::class,
            'funding_round_co_investors',
            'funding_round_id',
            'investor_id'
        );
    }

    public function getAmountInUsd(float $amount, string $currency, float $rate): int
    {
        if ($currency === 'USD') {
            return (int) $amount;
        }

        return (int) ($amount / $rate);
    }
}