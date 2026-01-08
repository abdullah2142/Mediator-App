<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MediationSession extends Model
{
    protected $table = 'mediation_sessions';

    protected $fillable = [
        'code',
        'status',
        'conflict_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants for clarity
    const STATUS_WAITING = 'waiting_for_partner';
    const STATUS_USER1_TURN = 'user1_turn';
    const STATUS_USER2_TURN = 'user2_turn';
    const STATUS_AI_RESPONDING = 'ai_responding';
    const STATUS_COMPLETED = 'completed';

    // Generate unique 6-character code
    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    // Relationships
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'session_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'session_id');
    }

    // Helper methods
    public function user1(): ?Participant
    {
        return $this->participants()->where('role', 'user1')->first();
    }

    public function user2(): ?Participant
    {
        return $this->participants()->where('role', 'user2')->first();
    }

    public function currentRound(): int
    {
        return $this->messages()->where('type', 'ai')->count() + 1;
    }

    public function getMessagesForRound(int $round): array
    {
        return $this->messages()
            ->where('round_number', $round)
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    public function isWaitingForPartner(): bool
    {
        return $this->status === self::STATUS_WAITING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
