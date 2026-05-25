<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'startup_id',
        'notification_type',
        'title',
        'message',
        'is_read',
        'priority',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }
}
