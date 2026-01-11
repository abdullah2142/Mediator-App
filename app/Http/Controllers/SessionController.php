<?php

namespace App\Http\Controllers;

use App\Models\MediationSession;
use App\Models\Message;
use App\Models\Participant;
use App\Models\UserMemory;
use App\Services\MediationService;
use App\Services\MemoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SessionController extends Controller
{
    private const FREE_ACTIVE_ROOMS_LIMIT = 1;
    private const PREMIUM_ACTIVE_ROOMS_LIMIT = 10;

    /**
     * Show the create session form
     */
    public function create()
    {
        return view('session.create', [
            'activeSession' => $this->getActiveSessionForGuest(),
        ]);
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

    if ($redirect = $this->denyIfRoomLimitReached()) {
        return $redirect;
    }

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

    // Store participant per-session (supports multiple rooms per browser)
    $this->rememberParticipant($session->code, $creator->id);

    return redirect()->route('session.room', $session->code);
}
    /**
     * Show the join session form
     */
    public function join()
    {
        return view('session.join', [
            'activeSession' => $this->getActiveSessionForGuest(),
        ]);
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

        // If already joined in this browser (or logged in), just go to the room.
        $existingParticipantId = $this->participantIdForCode($code);
        if ($existingParticipantId) {
            $existingParticipant = Participant::find($existingParticipantId);
            if ($existingParticipant && $existingParticipant->session_id === $session->id) {
                $this->rememberParticipant($code, $existingParticipant->id);
                return redirect()->route('session.room', $code);
            }
        }

        if (Auth::check()) {
            $existingByUser = Participant::where('session_id', $session->id)
                ->where('user_id', Auth::id())
                ->first();
            if ($existingByUser) {
                $this->rememberParticipant($code, $existingByUser->id);
                return redirect()->route('session.room', $code);
            }
        }

        if ($redirect = $this->denyIfRoomLimitReached()) {
            return $redirect;
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

        // Store participant per-session (supports multiple rooms per browser)
        $this->rememberParticipant($session->code, $participant->id);

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
        $participantId = $this->participantIdForCode($code);
        $currentParticipant = $participantId ? $session->participants->firstWhere('id', $participantId) : null;

        // If logged in, try to match by user_id
        if (!$currentParticipant && Auth::check()) {
            $currentParticipant = $session->participants->firstWhere('user_id', Auth::id());
        }

        if ($currentParticipant) {
            $this->rememberParticipant($code, $currentParticipant->id);
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

        // Memory feature data (for premium users)
        $user = Auth::user();
        $canSaveMemory = $user && $user->is_premium;
        $hasMemoryForSession = $canSaveMemory && UserMemory::where('user_id', $user->id)
            ->where('session_id', $session->id)
            ->exists();
        $defaultSaveLevel = $user?->getDefaultSaveLevel() ?? 'summary';

        // Get partner name for memory save
        $partnerName = null;
        if ($currentParticipant) {
            $otherRole = $currentParticipant->role === 'user1' ? 'user2' : 'user1';
            $partner = $session->participants->firstWhere('role', $otherRole);
            $partnerName = $partner?->name;
        }

        return view('session.room', compact(
            'session',
            'currentParticipant',
            'user1',
            'user2',
            'messagesByRound',
            'messagesJson',
            'canSaveMemory',
            'hasMemoryForSession',
            'defaultSaveLevel',
            'partnerName'
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
        $participantId = $this->participantIdForCode($code) ?? session('participant_id');
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
        $participantId = $this->participantIdForCode($code) ?? session('participant_id');
        $participant = Participant::find($participantId);

        if (!$participant || $participant->session_id !== $session->id) {
            return back()->withErrors(['error' => 'You cannot end this session.']);
        }

        $session->update(['status' => MediationSession::STATUS_COMPLETED]);

        return redirect()->route('session.room', $code);
    }

    /**
     * Save memory from a completed session (Premium feature)
     */
    public function saveMemory(Request $request, string $code, MemoryService $memoryService)
    {
        $user = Auth::user();

        if (!$user || !$user->is_premium) {
            return response()->json(['error' => 'Premium feature only'], 403);
        }

        $validated = $request->validate([
            'save_level' => 'required|in:all,summary,none',
            'partner_name' => 'nullable|string|max:50',
        ]);

        $session = MediationSession::where('code', $code)->firstOrFail();

        // Check if already saved
        $existing = UserMemory::where('user_id', $user->id)
            ->where('session_id', $session->id)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'Memory already saved for this session'], 409);
        }

        if ($validated['save_level'] === 'none') {
            return response()->json(['success' => true, 'message' => 'No memory saved']);
        }

        $memory = $memoryService->saveMemory(
            $user,
            $session,
            $validated['save_level'],
            $validated['partner_name'] ?? null
        );

        if (!$memory) {
            return response()->json(['error' => 'Failed to save memory'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Memory saved successfully',
            'memory_id' => $memory->id,
        ]);
    }

    public function rooms(): View
    {
        $sessionsByCode = collect();

        // Only show browser-based sessions if user is logged in
        // This prevents session data from previous users leaking to new guests
        if (Auth::check()) {
            $browserCodes = $this->browserSessionCodes();
            if ($browserCodes !== []) {
                $browserSessions = MediationSession::query()
                    ->whereIn('code', $browserCodes)
                    ->with(['participants'])
                    ->get();

                $sessionsByCode = $sessionsByCode->merge($browserSessions->keyBy('code'));
            }

            $participantSessions = Participant::query()
                ->where('user_id', Auth::id())
                ->with(['session.participants'])
                ->get()
                ->map(fn (Participant $p) => $p->session)
                ->filter()
                ->unique('id');

            $userSessions = $participantSessions->values();
            $sessionsByCode = $sessionsByCode->merge($userSessions->keyBy('code'));
        }

        $sessions = $sessionsByCode->values()->sortByDesc('updated_at')->values();

        $items = $sessions->map(function (MediationSession $session) {
            $role = null;
            $displayName = null;

            if (Auth::check()) {
                $participant = $session->participants->firstWhere('user_id', Auth::id());
                if ($participant) {
                    $role = $participant->role;
                    $displayName = $participant->name;
                }
            }

            if (!$role) {
                $participantId = $this->participantIdForCode($session->code);
                if ($participantId) {
                    $participant = $session->participants->firstWhere('id', $participantId);
                    if ($participant) {
                        $role = $participant->role;
                        $displayName = $participant->name;
                    }
                }
            }

            return [
                'code' => $session->code,
                'status' => $session->status,
                'conflict_type' => $session->conflict_type,
                'updated_at' => $session->updated_at,
                'role' => $role,
                'name' => $displayName,
                'is_active' => $session->status !== MediationSession::STATUS_COMPLETED,
            ];
        });

        return view('rooms.index', [
            'rooms' => $items,
            'activeCount' => $this->activeRoomsCount(),
            'activeLimit' => $this->activeRoomsLimit(),
            'isPremium' => (bool) (Auth::user()?->is_premium),
            'isLoggedIn' => Auth::check(),
        ]);
    }

    private function participantIdForCode(string $code): ?int
    {
        $participants = session('participants', []);
        if (!is_array($participants)) {
            $participants = [];
        }

        $code = strtoupper($code);
        $participantId = $participants[$code] ?? null;

        if (!$participantId && session('session_code') === $code) {
            $participantId = session('participant_id');
        }

        return is_numeric($participantId) ? (int) $participantId : null;
    }

    private function browserSessionCodes(): array
    {
        $participants = session('participants', []);
        if (!is_array($participants)) {
            $participants = [];
        }

        $codes = array_keys($participants);

        $legacyCode = session('session_code');
        if (is_string($legacyCode) && $legacyCode !== '' && !in_array(strtoupper($legacyCode), $codes, true)) {
            $codes[] = strtoupper($legacyCode);
        }

        return array_values(array_unique(array_map('strtoupper', $codes)));
    }

    private function rememberParticipant(string $code, int $participantId): void
    {
        $participants = session('participants', []);
        if (!is_array($participants)) {
            $participants = [];
        }

        $code = strtoupper($code);
        $participants[$code] = $participantId;

        session([
            'participants' => $participants,
            // keep legacy keys for backwards-compat + convenience
            'participant_id' => $participantId,
            'session_code' => $code,
            'last_session_code' => $code,
        ]);
    }

    private function denyIfRoomLimitReached(): ?RedirectResponse
    {
        $limit = $this->activeRoomsLimit();
        if ($this->activeRoomsCount() < $limit) {
            return null;
        }

        $message = $limit === self::FREE_ACTIVE_ROOMS_LIMIT
            ? 'Free users can only have 1 active room. End your current session to start another, or upgrade to Premium.'
            : "You’ve reached your active room limit ({$limit}). End a session to start another.";

        return back()->withErrors(['room_limit' => $message])->withInput();
    }

    private function activeRoomsLimit(): int
    {
        $user = Auth::user();
        if ($user && (bool) $user->is_premium) {
            return self::PREMIUM_ACTIVE_ROOMS_LIMIT;
        }

        return self::FREE_ACTIVE_ROOMS_LIMIT;
    }

    private function activeRoomsCount(): int
    {
        $browserCount = $this->countActiveRoomsForBrowser();
        $userCount = Auth::check() ? $this->countActiveRoomsForUser() : 0;

        return max($browserCount, $userCount);
    }

    private function countActiveRoomsForBrowser(): int
    {
        $codes = $this->browserSessionCodes();

        if ($codes === []) {
            return 0;
        }

        return MediationSession::query()
            ->whereIn('code', $codes)
            ->where('status', '!=', MediationSession::STATUS_COMPLETED)
            ->count();
    }

    private function countActiveRoomsForUser(): int
    {
        return Participant::query()
            ->where('user_id', Auth::id())
            ->whereHas('session', function ($query) {
                $query->where('status', '!=', MediationSession::STATUS_COMPLETED);
            })
            ->distinct()
            ->count('session_id');
    }

    /**
     * Get the first active session for the current browser (guest users)
     */
    private function getActiveSessionForGuest(): ?MediationSession
    {
        $codes = $this->browserSessionCodes();

        if ($codes === []) {
            return null;
        }

        return MediationSession::query()
            ->whereIn('code', $codes)
            ->where('status', '!=', MediationSession::STATUS_COMPLETED)
            ->first();
    }
}
