<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartupDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_id',
        'document_type',
        'document_name',
        'file_path',
        'file_size_kb',
        'uploaded_by',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }
}