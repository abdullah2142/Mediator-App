<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediationSession;
use Illuminate\Http\JsonResponse;

class SessionApiController extends Controller
{
    /**
     * Get session status for polling
     */
    public function status(string $code): JsonResponse
    {
        $session = MediationSession::where('code', $code)
            ->with(['participants', 'messages.participant'])
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $user1 = $session->user1();
        $user2 = $session->user2();
        $round = $session->messages()->where('type', 'ai')->count() + 1;

        // Format messages for response
        $messages = $session->messages->map(function ($message) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'round_number' => $message->round_number,
                'participant_name' => $message->participant?->name ?? 'Dr. Harmony',
                'participant_role' => $message->participant?->role ?? 'ai',
                'created_at' => $message->created_at->toISOString(),
            ];
        });

        // Determine whose turn and status message
        $turnInfo = $this->getTurnInfo($session, $user1, $user2);

        return response()->json([
            'code' => $session->code,
            'status' => $session->status,
            'conflict_type' => $session->conflict_type,
            'round' => $round,
            'user1' => $user1 ? [
                'id' => $user1->id,
                'name' => $user1->name,
            ] : null,
            'user2' => $user2 ? [
                'id' => $user2->id,
                'name' => $user2->name,
            ] : null,
            'messages' => $messages,
            'turn_info' => $turnInfo,
            'is_completed' => $session->isCompleted(),
        ]);
    }

    /**
     * Get turn information
     */
    private function getTurnInfo(MediationSession $session, $user1, $user2): array
    {
        $status = $session->status;

        return match ($status) {
            MediationSession::STATUS_WAITING => [
                'message' => 'Waiting for partner to join...',
                'current_turn' => null,
                'waiting' => true,
            ],
            MediationSession::STATUS_USER1_TURN => [
                'message' => "Waiting for {$user1->name} to share their perspective...",
                'current_turn' => 'user1',
                'waiting' => false,
            ],
            MediationSession::STATUS_USER2_TURN => [
                'message' => "Waiting for {$user2->name} to share their perspective...",
                'current_turn' => 'user2',
                'waiting' => false,
            ],
            MediationSession::STATUS_AI_RESPONDING => [
                'message' => 'Dr. Harmony is preparing a response...',
                'current_turn' => 'ai',
                'waiting' => false,
            ],
            MediationSession::STATUS_COMPLETED => [
                'message' => 'This mediation session has ended.',
                'current_turn' => null,
                'waiting' => false,
            ],
            default => [
                'message' => 'Unknown status',
                'current_turn' => null,
                'waiting' => false,
            ],
        };
    }
}
