<?php

namespace App\Services;

use App\Models\MediationSession;
use App\Models\User;
use App\Models\UserMemory;
use Illuminate\Support\Facades\Log;

class MemoryService
{
    public function __construct(
        private GroqService $groqService
    ) {}

    /**
     * Save a memory for a user from a completed session
     */
    public function saveMemory(
        User $user,
        MediationSession $session,
        string $saveLevel,
        ?string $partnerName = null
    ): ?UserMemory {
        if (!$user->is_premium) {
            return null;
        }

        if ($saveLevel === 'none') {
            return null;
        }

        // Get the other participant's name if not provided
        if ($partnerName === null) {
            $participant = $session->participants->firstWhere('user_id', $user->id);
            $otherParticipant = $session->participants->firstWhere('role', $participant?->role === 'user1' ? 'user2' : 'user1');
            $partnerName = $otherParticipant?->name;
        }

        // Generate summary and insights
        $summaryData = $this->generateSummaryAndInsights($session, $user);

        if (!$summaryData) {
            Log::error('Failed to generate memory summary', ['session_id' => $session->id]);
            return null;
        }

        // Build transcript if saving all
        $transcript = null;
        if ($saveLevel === UserMemory::LEVEL_ALL) {
            $transcript = $this->buildTranscript($session);
        }

        return UserMemory::create([
            'user_id' => $user->id,
            'session_id' => $session->id,
            'save_level' => $saveLevel,
            'partner_name' => $partnerName,
            'conflict_type' => $session->conflict_type,
            'transcript' => $transcript,
            'summary' => $summaryData['summary'],
            'insights' => $summaryData['insights'],
        ]);
    }

    /**
     * Generate AI summary and extract insights from a session
     */
    public function generateSummaryAndInsights(MediationSession $session, User $user): ?array
    {
        $messages = $session->messages()->with('participant')->orderBy('created_at')->get();

        if ($messages->isEmpty()) {
            return [
                'summary' => 'No messages exchanged in this session.',
                'insights' => ['themes' => [], 'patterns' => []],
            ];
        }

        $participant = $session->participants->firstWhere('user_id', $user->id);
        $userRole = $participant?->role ?? 'user1';

        $transcript = $this->buildTranscript($session);

        $systemPrompt = <<<PROMPT
You are an assistant that summarizes mediation sessions. Given a transcript of a mediation conversation, provide:

1. A concise summary (2-3 paragraphs) of what was discussed and any resolutions reached
2. Key themes that emerged (as a JSON array of strings)
3. Notable patterns in communication or recurring issues (as a JSON array of strings)

The user you're summarizing for was "{$userRole}" in this conversation.

Respond in this exact JSON format:
{
    "summary": "Your summary here...",
    "insights": {
        "themes": ["theme1", "theme2"],
        "patterns": ["pattern1", "pattern2"]
    }
}
PROMPT;

        $userMessage = <<<MSG
Conflict type: {$session->conflict_type}

Transcript:
{$transcript}
MSG;

        $response = $this->groqService->chat($systemPrompt, $userMessage, [
            'temperature' => 0.5,
            'max_tokens' => 1024,
        ]);

        if (!$response) {
            return null;
        }

        try {
            // Extract JSON from response (handle markdown code blocks)
            $jsonString = $response;
            if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $response, $matches)) {
                $jsonString = $matches[1];
            }

            $data = json_decode(trim($jsonString), true);

            if (isset($data['summary']) && isset($data['insights'])) {
                return $data;
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse memory summary response', [
                'error' => $e->getMessage(),
                'response' => $response,
            ]);
        }

        // Fallback: use raw response as summary
        return [
            'summary' => $response,
            'insights' => ['themes' => [], 'patterns' => []],
        ];
    }

    /**
     * Build a readable transcript from session messages
     */
    public function buildTranscript(MediationSession $session): string
    {
        $messages = $session->messages()->with('participant')->orderBy('created_at')->get();

        $lines = [];
        $currentRound = 0;

        foreach ($messages as $message) {
            if ($message->round_number !== $currentRound) {
                $currentRound = $message->round_number;
                $lines[] = "\n--- Round {$currentRound} ---\n";
            }

            $speaker = $message->participant?->name ?? 'Dr. Harmony';
            $role = $message->participant?->role ?? 'AI Mediator';

            $lines[] = "[{$speaker} ({$role})]: {$message->content}\n";
        }

        return implode("\n", $lines);
    }

    /**
     * Get memory context for a user to inject into AI prompts
     */
    public function getMemoryContext(User $user, ?string $conflictType = null): ?string
    {
        if (!$user->hasMemoryEnabled()) {
            return null;
        }

        $query = $user->memories()->orderByDesc('created_at')->limit(10);

        if ($conflictType) {
            // Prioritize same conflict type, but include others
            $sameType = (clone $query)->where('conflict_type', $conflictType)->limit(5)->get();
            $otherTypes = (clone $query)->where('conflict_type', '!=', $conflictType)->limit(3)->get();
            $memories = $sameType->concat($otherTypes);
        } else {
            $memories = $query->get();
        }

        if ($memories->isEmpty()) {
            return null;
        }

        $context = "[USER MEMORY CONTEXT]\n";
        $context .= "Sessions on record: {$memories->count()}\n\n";

        // Aggregate themes and patterns across memories
        $allThemes = [];
        $allPatterns = [];

        foreach ($memories as $memory) {
            $allThemes = array_merge($allThemes, $memory->getThemes());
            $allPatterns = array_merge($allPatterns, $memory->getPatterns());
        }

        // Count occurrences to find recurring themes
        $themeCounts = array_count_values($allThemes);
        $patternCounts = array_count_values($allPatterns);
        arsort($themeCounts);
        arsort($patternCounts);

        $recurringThemes = array_slice(array_keys($themeCounts), 0, 5);
        $recurringPatterns = array_slice(array_keys($patternCounts), 0, 3);

        if (!empty($recurringThemes)) {
            $context .= "Recurring themes:\n";
            foreach ($recurringThemes as $theme) {
                $context .= "• {$theme}\n";
            }
            $context .= "\n";
        }

        if (!empty($recurringPatterns)) {
            $context .= "Communication patterns observed:\n";
            foreach ($recurringPatterns as $pattern) {
                $context .= "• {$pattern}\n";
            }
            $context .= "\n";
        }

        // Add recent session summaries
        $recentMemories = $memories->take(3);
        $context .= "Recent sessions:\n";
        foreach ($recentMemories as $memory) {
            $date = $memory->created_at->format('M j');
            $partner = $memory->partner_name ? "with {$memory->partner_name}" : '';
            $context .= "• {$date} ({$memory->conflict_type} {$partner}): ";
            $context .= substr($memory->summary, 0, 150) . "...\n";
        }

        $context .= "\nInstructions: Reference this context naturally when relevant. ";
        $context .= "You might say things like \"I recall from our previous conversations...\" ";
        $context .= "or \"I notice this theme has come up before...\"\n";

        return $context;
    }

    /**
     * Delete a user's memory
     */
    public function deleteMemory(UserMemory $memory): bool
    {
        return $memory->delete();
    }

    /**
     * Delete all memories for a user
     */
    public function deleteAllMemories(User $user): int
    {
        return $user->memories()->delete();
    }
}
