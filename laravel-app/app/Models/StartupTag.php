<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartupTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_id',
        'tag',
    ];

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }
}