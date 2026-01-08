<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'session_id',
        'participant_id',
        'content',
        'type',
        'round_number',
    ];

    protected $casts = [
        'round_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function session(): BelongsTo
    {
        return $this->belongsTo(MediationSession::class, 'session_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    // Helper methods
    public function isFromAI(): bool
    {
        return $this->type === 'ai';
    }

    public function isFromUser(): bool
    {
        return $this->type === 'user';
    }
}
