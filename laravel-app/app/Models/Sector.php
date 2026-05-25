<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'sector_name',
        'description',
        'is_priority_sector',
    ];

    protected $casts = [
        'is_priority_sector' => 'string',
    ];

    public function startups()
    {
        return $this->hasMany(Startup::class);
    }
}