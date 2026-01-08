<?php

namespace App\Http\Controllers;

use App\Models\MediationSession;
use App\Models\Message;
use App\Models\Participant;
use App\Services\MediationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Show the create session form
     */
    public function create()
    {
        return view('session.create');
    }

    /**
     * Store a new session
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:50',
        // Make optional so landing page can create session without extra step
        'conflict_type' => 'nullable|in:relationship,family,roommate,workplace,friendship,other',
    ]);

    $conflictType = $validated['conflict_type'] ?? 'other';

    // Create session with unique code
    $session = MediationSession::create([
        'code' => MediationSession::generateCode(),
        'status' => MediationSession::STATUS_WAITING,
        'conflict_type' => $conflictType,
    ]);

    // Add creator as first participant
    $creator = Participant::create([
        'session_id' => $session->id,
        'user_id' => Auth::id(), // null for guests
        'name' => $validated['name'],
        'role' => 'user1',
    ]);

    // Store participant ID + session code (guest + simple tracking)
    session([
        'participant_id' => $creator->id,
        'session_code' => $session->code,
    ]);

    return redirect()->route('session.room', $session->code);
}
    /**
     * Show the join session form
     */
    public function join()
    {
        return view('session.join');
    }

    /**
     * Join an existing session
     */
    public function doJoin(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
            'name' => 'required|string|max:50',
        ]);

        $code = strtoupper($validated['code']);

        $session = MediationSession::where('code', $code)->first();

        if (!$session) {
            return back()->withErrors(['code' => 'Session not found. Please check the code.']);
        }

        if ($session->status !== MediationSession::STATUS_WAITING) {
            return back()->withErrors(['code' => 'This session already has two participants or has ended.']);
        }

        // Add as second participant
        $participant = Participant::create([
            'session_id' => $session->id,
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'role' => 'user2',
        ]);

        // Update session status to start
        $session->update(['status' => MediationSession::STATUS_USER1_TURN]);

        // Store for guest users
        session(['participant_id' => $participant->id]);
        session(['session_code' => $session->code]);

        return redirect()->route('session.room', $session->code);
    }

    /**
     * Show the mediation room
     */
    public function room(string $code)
    {
        $session = MediationSession::where('code', $code)
            ->with(['participants', 'messages.participant'])
            ->firstOrFail();

        // Determine current participant
        $participantId = session('participant_id');
        $currentParticipant = $session->participants->firstWhere('id', $participantId);

        // If logged in, try to match by user_id
        if (!$currentParticipant && Auth::check()) {
            $currentParticipant = $session->participants->firstWhere('user_id', Auth::id());
        }

        $user1 = $session->user1();
        $user2 = $session->user2();

        // Group messages by round
        $messagesByRound = $session->messages->groupBy('round_number');

        // Pre-format messages for JavaScript
        $messagesJson = $session->messages->map(function ($m) {
            return [
                'id' => $m->id,
                'content' => $m->content,
                'type' => $m->type,
                'round_number' => $m->round_number,
                'participant_name' => $m->participant?->name ?? 'Dr. Harmony',
                'participant_role' => $m->participant?->role ?? 'ai',
                'created_at' => $m->created_at->toISOString(),
            ];
        })->values();

        return view('session.room', compact(
            'session',
            'currentParticipant',
            'user1',
            'user2',
            'messagesByRound',
            'messagesJson'
        ));
    }

    /**
     * Submit a message
     */
    public function sendMessage(Request $request, string $code, MediationService $mediationService)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:10|max:5000',
        ]);

        $session = MediationSession::where('code', $code)->firstOrFail();

        // Get current participant
        $participantId = session('participant_id');
        $participant = Participant::find($participantId);

        if (!$participant || $participant->session_id !== $session->id) {
            return back()->withErrors(['content' => 'You are not a participant in this session.']);
        }

        // Check if it's their turn
        $isUser1Turn = $session->status === MediationSession::STATUS_USER1_TURN && $participant->role === 'user1';
        $isUser2Turn = $session->status === MediationSession::STATUS_USER2_TURN && $participant->role === 'user2';

        if (!$isUser1Turn && !$isUser2Turn) {
            return back()->withErrors(['content' => 'It is not your turn to speak.']);
        }

        // Calculate round number
        $round = $session->messages()->where('type', 'ai')->count() + 1;

        // Save message
        Message::create([
            'session_id' => $session->id,
            'participant_id' => $participant->id,
            'content' => $validated['content'],
            'type' => 'user',
            'round_number' => $round,
        ]);

        // Update session status
        if ($participant->role === 'user1') {
            $session->update(['status' => MediationSession::STATUS_USER2_TURN]);
        } else {
            // User 2 submitted, now AI responds
            $session->update(['status' => MediationSession::STATUS_AI_RESPONDING]);

            // Trigger AI response
            $mediationService->processTurn($session);
        }

        return redirect()->route('session.room', $code);
    }

    /**
     * End the session
     */
    public function end(string $code)
    {
        $session = MediationSession::where('code', $code)->firstOrFail();

        // Verify participant
        $participantId = session('participant_id');
        $participant = Participant::find($participantId);

        if (!$participant || $participant->session_id !== $session->id) {
            return back()->withErrors(['error' => 'You cannot end this session.']);
        }

        $session->update(['status' => MediationSession::STATUS_COMPLETED]);

        return redirect()->route('session.room', $code);
    }
}
