<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AnalyticsActiveSession extends Model
{
    protected $table = 'analytics_active_sessions';

    protected $fillable = [
        'session_id',
        'ip_address',
        'current_url',
        'current_title',
        'route_name',
        'viewable_type',
        'viewable_id',
        'referrer',
        'referrer_domain',
        'device_type',
        'browser',
        'platform',
        'first_seen_at',
        'last_active_at',
    ];

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_active_at' => 'datetime',
        ];
    }

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to only get active sessions in the last N minutes (default 3 minutes)
     */
    public function scopeActive(Builder $query, int $minutes = 3): Builder
    {
        return $query->where('last_active_at', '>=', now()->subMinutes($minutes));
    }
}
