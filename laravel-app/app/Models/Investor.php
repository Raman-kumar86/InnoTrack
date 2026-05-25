<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_name',
        'investor_type',
        'city',
        'state_id',
        'aum_crore',
        'portfolio_count',
        'website',
        'email',
    ];

    public function fundingRounds()
    {
        return $this->hasMany(FundingRound::class);
    }

    public function startupInvestments()
    {
        return $this->hasMany(StartupInvestor::class);
    }
}
