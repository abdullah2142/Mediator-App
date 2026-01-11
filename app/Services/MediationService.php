<?php

namespace App\Services;

use App\Models\MediationSession;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MediationService
{
    private GroqService $groq;
    private MemoryService $memoryService;

    public function __construct(GroqService $groq, MemoryService $memoryService)
    {
        $this->groq = $groq;
        $this->memoryService = $memoryService;
    }

    /**
     * Generate AI mediation response for the current round
     */
    public function generateResponse(MediationSession $session): ?string
    {
        $user1 = $session->user1();
        $user2 = $session->user2();
        $round = $session->currentRound();

        // Get messages for current round from both users
        $roundMessages = $session->messages()
            ->where('round_number', $round)
            ->where('type', 'user')
            ->with('participant')
            ->get();

        $user1Message = $roundMessages->firstWhere('participant.role', 'user1');
        $user2Message = $roundMessages->firstWhere('participant.role', 'user2');

        if (!$user1Message || !$user2Message) {
            Log::error('Missing user messages for round', ['round' => $round]);
            return null;
        }

        // Build previous rounds summary
        $previousSummary = $this->buildPreviousSummary($session, $round);

        // Build the system prompt with memory context if available
        $systemPrompt = $this->buildSystemPrompt();

        // Inject memory context for premium users
        $memoryContext = $this->buildMemoryContext($session);
        if ($memoryContext) {
            $systemPrompt .= "\n\n" . $memoryContext;
        }

        // Build user message with context
        $userMessage = $this->buildUserMessage(
            $session->conflict_type,
            $round,
            $user1->name,
            $user1Message->content,
            $user2->name,
            $user2Message->content,
            $previousSummary
        );

        return $this->groq->chat($systemPrompt, $userMessage, [
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ]);
    }

    /**
     * Build memory context from participants' saved memories
     */
    private function buildMemoryContext(MediationSession $session): ?string
    {
        $contexts = [];

        // Check each participant for memory context
        foreach ($session->participants as $participant) {
            if (!$participant->user_id) {
                continue; // Skip guest participants
            }

            $user = User::find($participant->user_id);
            if (!$user || !$user->hasMemoryEnabled()) {
                continue;
            }

            $memoryContext = $this->memoryService->getMemoryContext($user, $session->conflict_type);
            if ($memoryContext) {
                $contexts[] = "--- Memory context for {$participant->name} ({$participant->role}) ---\n" . $memoryContext;
            }
        }

        if (empty($contexts)) {
            return null;
        }

        return implode("\n\n", $contexts);
    }

    /**
     * Build the Dr. Harmony system prompt
     */
    private function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
You are Dr. Harmony, an AI relationship and conflict mediator with expertise in evidence-based therapeutic approaches.

YOUR CREDENTIALS & FRAMEWORKS:
You are trained in and actively apply:
- Gottman Method Couples Therapy (The Four Horsemen and their antidotes)
- Nonviolent Communication (NVC) by Marshall Rosenberg
- Emotionally Focused Therapy (EFT) by Dr. Sue Johnson
- Cognitive Behavioral approaches to conflict resolution
- Harvard Negotiation Project principles (interests vs positions)

RESEARCH YOU CAN CITE:
- "Dr. John Gottman's research, spanning 40+ years, can predict relationship outcomes with 94% accuracy based on communication patterns."
- "The Gottman Institute found that successful couples have a 5:1 ratio of positive to negative interactions."
- "Studies show 69% of relationship conflicts are perpetual - the goal is dialogue, not resolution."
- "Research indicates physiological flooding (heart rate >100bpm) makes productive conversation neurologically impossible."
- "The Harvard Negotiation Project found that 85% of conflicts resolve when underlying needs are addressed rather than surface positions."
- "Dr. Sue Johnson's EFT research shows that attachment security is the foundation of healthy relationships."

YOUR MEDIATION PROCESS:
After receiving input from both parties, you MUST:

1. ACKNOWLEDGE both people by name and validate their emotional experience
2. IDENTIFY the underlying needs/emotions beneath their stated positions
3. FIND common ground - what do both parties actually want?
4. REFRAME criticisms as expressions of unmet needs using NVC format
5. CITE relevant research when it strengthens your point (naturally, not forcefully)
6. DETECT any of the Four Horsemen (criticism, contempt, defensiveness, stonewalling) and gently name them with their antidotes
7. SUGGEST one concrete, actionable next step or communication exercise
8. ASK one reflective question to deepen understanding for the next round

TONE GUIDELINES:
- Warm, professional, and non-judgmental
- Never take sides or assign blame
- Normalize conflict as healthy when handled constructively
- Use "I notice..." and "It sounds like..." language
- Keep responses focused and actionable (aim for 200-350 words)
- Address both parties directly by name

CONFLICT TYPE ADAPTATIONS:
- Relationship/Family: Focus on attachment, emotional bids, repair attempts
- Workplace: Focus on professional boundaries, interests vs positions, collaborative problem-solving
- Roommate: Focus on expectations, boundaries, practical compromises
- Friendship: Focus on communication styles, unspoken expectations, mutual respect
PROMPT;
    }

    /**
     * Build the user message with all context
     */
    private function buildUserMessage(
        string $conflictType,
        int $round,
        string $name1,
        string $message1,
        string $name2,
        string $message2,
        string $previousSummary
    ): string {
        $message = "CURRENT SESSION CONTEXT:\n";
        $message .= "Conflict Type: {$conflictType}\n";
        $message .= "Round: {$round}\n\n";

        $message .= "{$name1}'s perspective:\n\"{$message1}\"\n\n";
        $message .= "{$name2}'s perspective:\n\"{$message2}\"\n\n";

        if (!empty($previousSummary)) {
            $message .= "Previous rounds summary:\n{$previousSummary}\n\n";
        }

        $message .= "Provide your mediation response now.";

        return $message;
    }

    /**
     * Build summary of previous rounds
     */
    private function buildPreviousSummary(MediationSession $session, int $currentRound): string
    {
        if ($currentRound <= 1) {
            return '';
        }

        $summaries = [];

        for ($r = 1; $r < $currentRound; $r++) {
            $aiMessage = $session->messages()
                ->where('round_number', $r)
                ->where('type', 'ai')
                ->first();

            if ($aiMessage) {
                // Truncate long AI responses for context
                $content = strlen($aiMessage->content) > 300
                    ? substr($aiMessage->content, 0, 300) . '...'
                    : $aiMessage->content;

                $summaries[] = "Round {$r} mediation: {$content}";
            }
        }

        return implode("\n\n", $summaries);
    }

    /**
     * Process the turn and generate AI response if both users have submitted
     */
    public function processTurn(MediationSession $session): bool
    {
        // This is called after a user submits their message
        // Check if we should generate AI response

        if ($session->status !== MediationSession::STATUS_AI_RESPONDING) {
            return false;
        }

        $response = $this->generateResponse($session);

        if (!$response) {
            Log::error('Failed to generate AI response', ['session' => $session->id]);
            return false;
        }

        // Save AI message
        Message::create([
            'session_id' => $session->id,
            'participant_id' => null,
            'content' => $response,
            'type' => 'ai',
            'round_number' => $session->currentRound(),
        ]);

        // Move to next round - user1's turn again
        $session->update(['status' => MediationSession::STATUS_USER1_TURN]);

        return true;
    }
}
