<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }

    public function getRelativeTimeAttribute(): string
    {
        return $this->created_at?->diffForHumans() ?? 'Recently';
    }
}
