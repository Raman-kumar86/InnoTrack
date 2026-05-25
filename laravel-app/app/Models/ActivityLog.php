<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'causer_id',
        'target_user_id',
        'user_id',
        'user_name',
        'module',
        'action',
        'result',
        'ip_address',
        'user_agent',
        'loggable_type',
        'loggable_id',
        'metadata',
        'description',
        'icon',
        'is_system',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_system' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'System',
        ]);
    }

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id')->withDefault([
            'name' => 'System',
            'role' => 'super_admin',
        ]);
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id')->withDefault([
            'name' => 'Unknown User',
            'role' => 'reviewer',
        ]);
    }

    public function loggable()
    {
        return $this->morphTo();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, static function (Builder $query, string $term): void {
            $query->where(static function (Builder $query) use ($term): void {
                $query->where('user_name', 'like', '%' . $term . '%')
                    ->orWhere('action', 'like', '%' . $term . '%')
                    ->orWhere('module', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');
            });
        });
    }

    public function scopeFilterModule(Builder $query, ?string $module): Builder
    {
        return $query->when($module, static fn (Builder $query, string $module): Builder => $query->where('module', $module));
    }

    public function scopeFilterResult(Builder $query, ?string $result): Builder
    {
        return $query->when($result, static fn (Builder $query, string $result): Builder => $query->where('result', $result));
    }

    public function scopeFilterUser(Builder $query, ?string $userId): Builder
    {
        return $query->when($userId, static fn (Builder $query, string $userId): Builder => $query->where('causer_id', $userId));
    }

    public function scopeFilterAction(Builder $query, ?string $action): Builder
    {
        return $query->when($action, static fn (Builder $query, string $action): Builder => $query->where('action', $action));
    }

    public function scopeFilterRole(Builder $query, ?string $role): Builder
    {
        return $query->when($role, static function (Builder $query, string $role): void {
            $query->whereHas('causer', static fn (Builder $query): Builder => $query->where('role', $role));
        });
    }

    public function scopeFilterDateRange(Builder $query, ?string $range, ?string $dateFrom, ?string $dateTo): Builder
    {
        return match ($range) {
            'yesterday' => $query->whereDate('created_at', today()->subDay()),
            'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'custom' => $query
                ->when($dateFrom, static fn (Builder $query, string $dateFrom): Builder => $query->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, static fn (Builder $query, string $dateTo): Builder => $query->whereDate('created_at', '<=', $dateTo)),
            default => $query->whereDate('created_at', today()),
        };
    }

    public function getResultColorAttribute(): string
    {
        return match ($this->result) {
            'Success' => 'green',
            'Failed' => 'rose',
            'Blocked' => 'amber',
            'Pending' => 'blue',
            default => 'slate',
        };
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('H:i');
    }

    public function getFormattedDateAttribute(): string
    {
        if ($this->created_at->isToday()) {
            return 'Today, ' . $this->created_at->format('H:i');
        }

        if ($this->created_at->isYesterday()) {
            return 'Yesterday, ' . $this->created_at->format('H:i');
        }

        return $this->created_at->format('d M Y, H:i');
    }
}