<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatConversation extends Model
{
    protected $fillable = [
        'customer_token', 'customer_name', 'customer_phone',
        'status', 'assigned_agent_id', 'last_message_at',
        'closed_at', 'closed_by',
    ];

    /** Conversations idle this many minutes with no manual close get auto-closed. */
    const AUTO_CLOSE_AFTER_MINUTES = 15;

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Close any open conversation that has had no activity for
     * AUTO_CLOSE_AFTER_MINUTES, mimicking a manual close.
     */
    public static function autoCloseStale(): void
    {
        $cutoff = now()->subMinutes(self::AUTO_CLOSE_AFTER_MINUTES);

        static::where('status', 'open')
            ->where(function ($query) use ($cutoff) {
                $query->where('last_message_at', '<', $cutoff)
                    ->orWhere(function ($query) use ($cutoff) {
                        $query->whereNull('last_message_at')->where('created_at', '<', $cutoff);
                    });
            })
            ->update(['status' => 'closed', 'closed_at' => now(), 'closed_by' => 'auto']);
    }

    public function close(string $closedBy): void
    {
        if ($this->status === 'open') {
            $this->update(['status' => 'closed', 'closed_at' => now(), 'closed_by' => $closedBy]);
        }
    }

    protected static function booted(): void
    {
        static::creating(function (ChatConversation $conversation) {
            $conversation->customer_token ??= (string) Str::uuid();
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }
}
