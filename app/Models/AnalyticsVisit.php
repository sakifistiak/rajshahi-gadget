<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AnalyticsVisit extends Model
{
    public $timestamps = false;

    protected $table = 'analytics_visits';

    protected $fillable = [
        'session_id',
        'visitor_hash',
        'ip_address',
        'url',
        'route_name',
        'page_title',
        'viewable_type',
        'viewable_id',
        'referrer',
        'referrer_domain',
        'device_type',
        'browser',
        'platform',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePeriod(Builder $query, string $period = '7d'): Builder
    {
        return match ($period) {
            'today' => $query->whereDate('created_at', today()),
            'yesterday' => $query->whereDate('created_at', today()->subDay()),
            '7d' => $query->where('created_at', '>=', now()->subDays(7)),
            '30d' => $query->where('created_at', '>=', now()->subDays(30)),
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'all' => $query,
            default => $query->where('created_at', '>=', now()->subDays(7)),
        };
    }
}
