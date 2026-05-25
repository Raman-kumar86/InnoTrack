<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartupUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_id',
        'update_type',
        'title',
        'description',
        'update_date',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'update_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }
}