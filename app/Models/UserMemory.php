<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMemory extends Model
{
    use SoftDeletes;

    public const LEVEL_ALL = 'all';
    public const LEVEL_SUMMARY = 'summary';

    protected $fillable = [
        'user_id',
        'session_id',
        'save_level',
        'partner_name',
        'conflict_type',
        'transcript',
        'summary',
        'insights',
    ];

    protected function casts(): array
    {
        return [
            'insights' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(MediationSession::class, 'session_id');
    }

    /**
     * Check if this memory includes the full transcript
     */
    public function hasTranscript(): bool
    {
        return $this->save_level === self::LEVEL_ALL && $this->transcript !== null;
    }

    /**
     * Get formatted insights for display
     */
    public function getThemes(): array
    {
        return $this->insights['themes'] ?? [];
    }

    public function getPatterns(): array
    {
        return $this->insights['patterns'] ?? [];
    }
}
