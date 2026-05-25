<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_id',
        'round_type',
        'amount_raised_usd',
        'investor_id',
        'funding_date',
        'equity_diluted_percent',
        'valuation_after_round_usd',
        'lead_investor',
        'currency',
    ];

    protected $casts = [
        'funding_date' => 'date',
    ];

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }
}