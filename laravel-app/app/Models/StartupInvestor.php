<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartupInvestor extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_id',
        'investor_id',
        'investment_amount_usd',
        'investment_date',
        'equity_stake_percent',
        'investment_stage',
    ];

    protected $casts = [
        'investment_date' => 'date',
        'equity_stake_percent' => 'float',
    ];

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
