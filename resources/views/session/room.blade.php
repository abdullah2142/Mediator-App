@php
    // Avoid dynamic Tailwind class strings that can be purged by build.
    $conflictStyles = [
        'relationship' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'family'       => 'bg-amber-50 text-amber-700 ring-amber-200',
        'roommate'     => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'workplace'    => 'bg-blue-50 text-blue-700 ring-blue-200',
        'friendship'   => 'bg-violet-50 text-violet-700 ring-violet-200',
        'other'        => 'bg-slate-50 text-slate-700 ring-slate-200',
    ];
    $conflictClass = $conflictStyles[$session->conflict_type] ?? $conflictStyles['other'];

    $initialRound = $session->messages->where('type', 'ai')->count() + 1;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Session {{ $session->code }} - MediAItor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50 text-slate-800">
    <!-- Top Bar -->
    <nav class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/70 backdrop-blur">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('home') }}" class="group flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-blue-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold leading-tight text-slate-900">MediAItor</div>
                    <div class="text-xs text-slate-500">Session room</div>
                </div>
            </a>

            <div class="flex flex-wrap items-center justify-end gap-2">
                <!-- Code pill -->
                <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                    <span class="text-xs text-slate-500">Code</span>
                    <span id="sessionCode" class="font-mono text-sm font-semibold tracking-wider text-slate-900">{{ $session->code }}</span>

                    <button
                        type="button"
                        id="copyCodeBtn"
                        class="ml-1 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-100 active:scale-[0.98] transition"
                        aria-label="Copy session code"
                    >
                        Copy
                    </button>
                </div>

                <!-- Conflict badge -->
                <div class="rounded-xl ring-1 {{ $conflictClass }} px-3 py-2 text-xs font-semibold capitalize">
                    {{ $session->conflict_type }}
                </div>

                <!-- Optional: show who you are -->
                @if($currentParticipant)
                    <div class="hidden sm:flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                        <span class="text-xs text-slate-500">You</span>
                        <span class="text-xs font-semibold text-slate-800">{{ $currentParticipant->name }}</span>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-4 py-8" x-data="mediationRoom()" x-init="startPolling()">
        <!-- Header card -->
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-400 to-blue-500">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">Dr. Harmony</h2>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">AI Mediator</span>
                        </div>

                        <div class="mt-1 flex flex-wrap gap-2 text-[11px]">
                            <span class="rounded-full bg-teal-50 px-2 py-1 font-semibold text-teal-700 ring-1 ring-teal-200">Gottman</span>
                            <span class="rounded-full bg-blue-50 px-2 py-1 font-semibold text-blue-700 ring-1 ring-blue-200">NVC</span>
                            <span class="rounded-full bg-violet-50 px-2 py-1 font-semibold text-violet-700 ring-1 ring-violet-200">EFT</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 sm:justify-end">
                    <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                        <span class="text-slate-500">Round</span>
                        <span class="ml-1 font-semibold text-slate-900" x-text="round">{{ $initialRound }}</span>
                    </div>

                    @if(!$session->isCompleted() && $currentParticipant)
                        <form method="POST" action="{{ route('session.end', $session->code) }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100 active:scale-[0.98] transition">
                                End session
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Status line -->
            <div class="mt-4 flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3"
                 :class="{ 'ring-2 ring-teal-200 border-teal-200': isMyTurn }">
                <div class="h-2.5 w-2.5 rounded-full"
                     :class="status === 'completed' ? 'bg-slate-400' : (status === 'ai_responding' ? 'bg-amber-400 animate-pulse' : 'bg-emerald-500 animate-pulse')">
                </div>
                <div class="text-sm font-medium text-slate-700" x-text="turnMessage">
                    @if($session->status === 'waiting_for_partner')
                        Waiting for partner to join...
                    @elseif($session->status === 'user1_turn')
                        Waiting for {{ $user1?->name ?? 'User 1' }} to share their perspective...
                    @elseif($session->status === 'user2_turn')
                        Waiting for {{ $user2?->name ?? 'User 2' }} to share their perspective...
                    @elseif($session->status === 'ai_responding')
                        Dr. Harmony is preparing a response...
                    @else
                        Session completed
                    @endif
                </div>

                <div class="ml-auto text-xs text-slate-500" x-show="currentRole">
                    <span class="hidden sm:inline">Input:</span>
                    <span class="font-semibold" :class="isMyTurn ? 'text-teal-700' : 'text-slate-600'" x-text="isMyTurn ? 'Unlocked' : 'Locked'"></span>
                </div>
            </div>
        </section>

        <!-- Waiting for partner -->
        @if($session->status === 'waiting_for_partner')
            <section class="rounded-2xl border border-slate-200 bg-white/80 p-8 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-50 ring-1 ring-teal-200">
                    <svg class="h-7 w-7 text-teal-700 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-slate-900">Waiting for your partner</h3>
                <p class="mt-2 text-slate-600">Share this code with the other person:</p>

                <div class="mt-4 inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <span class="text-3xl font-mono font-bold tracking-widest text-slate-900">{{ $session->code }}</span>
                    <button type="button"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 active:scale-[0.98] transition"
                            onclick="copySessionCode('{{ $session->code }}')">
                        Copy
                    </button>
                </div>

                <p class="mt-4 text-sm text-slate-500">This page updates automatically once they join.</p>
            </section>
        @else
            <!-- Conversation -->
            <section class="rounded-2xl border border-slate-200 bg-white/70 p-4 shadow-sm">
                <div class="max-h-[62vh] overflow-y-auto px-2 py-2" id="messages-container">
                    <template x-for="r in rounds" :key="r.round">
                        <div class="mb-6">
                            <!-- Round separator -->
                            <div class="my-5 flex items-center gap-3">
                                <div class="h-px flex-1 bg-slate-200"></div>
                                <div class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                    Round <span x-text="r.round"></span>
                                </div>
                                <div class="h-px flex-1 bg-slate-200"></div>
                            </div>

                            <!-- Messages in round -->
                            <template x-for="msg in r.messages" :key="msg.id">
                                <div class="mb-3">
                                    <!-- USER -->
                                    <template x-if="msg.type === 'user'">
                                        <div class="flex w-full"
                                             :class="msg.participant_role === 'user2' ? 'justify-end' : 'justify-start'">
                                            <div class="max-w-[92%] sm:max-w-[78%]">
                                                <div class="mb-1 flex items-center gap-2 text-xs text-slate-500"
                                                     :class="msg.participant_role === 'user2' ? 'justify-end' : 'justify-start'">
                                                    <span class="font-semibold text-slate-700" x-text="msg.participant_name"></span>
                                                    <span>•</span>
                                                    <span x-text="formatTime(msg.created_at)"></span>
                                                </div>

                                                <div class="rounded-2xl border bg-white px-4 py-3 shadow-sm"
                                                     :class="msg.participant_role === 'user2'
                                                        ? 'border-blue-200 bg-blue-50/40'
                                                        : 'border-teal-200 bg-teal-50/40'">
                                                    <p class="whitespace-pre-wrap text-sm leading-relaxed text-slate-800" x-text="msg.content"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- AI -->
                                    <template x-if="msg.type === 'ai'">
                                        <div class="mx-auto max-w-[92%] sm:max-w-[78%]">
                                            <div class="mb-1 flex items-center justify-center gap-2 text-xs text-slate-500">
                                                <span class="font-semibold text-slate-700">Dr. Harmony</span>
                                                <span>•</span>
                                                <span x-text="formatTime(msg.created_at)"></span>
                                            </div>

                                            <div class="rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 px-5 py-4 text-slate-100 shadow-sm">
                                                <p class="whitespace-pre-wrap text-sm leading-relaxed text-slate-100" x-text="msg.content"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- AI “thinking” card -->
                    <div x-show="status === 'ai_responding'" class="mx-auto my-6 max-w-[92%] sm:max-w-[78%]">
                        <div class="rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 px-5 py-4 text-white shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-teal-400 to-blue-500">
                                    <svg class="h-4 w-4 animate-pulse text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                                <div class="text-sm">
                                    <div class="font-semibold">Dr. Harmony</div>
                                    <div class="text-xs text-slate-300">is thinking…</div>
                                </div>

                                <div class="ml-auto flex gap-1">
                                    <div class="h-2 w-2 rounded-full bg-teal-300 animate-bounce" style="animation-delay: 0s"></div>
                                    <div class="h-2 w-2 rounded-full bg-teal-300 animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="h-2 w-2 rounded-full bg-teal-300 animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Composer -->
            @if($currentParticipant && !$session->isCompleted())
                <section class="mt-5 rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm">
                    <form method="POST" action="{{ route('session.message', $session->code) }}" class="space-y-3">
                        @csrf

                        <div class="flex items-center justify-between">
                            <label class="text-sm font-semibold text-slate-800">Your message</label>
                            <span class="text-xs text-slate-500">
                                <span x-text="content.length"></span>/5000
                            </span>
                        </div>

                        <textarea
                            name="content"
                            rows="4"
                            required minlength="10" maxlength="5000"
                            x-model="content"
                            :disabled="!canWrite"
                            class="w-full resize-none rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-slate-800 shadow-sm
                                   focus:border-teal-400 focus:ring-2 focus:ring-teal-200
                                   disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed"
                            placeholder="Take your time. Describe what happened, how you feel, and what you need."
                        ></textarea>

                        @error('content')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-xs text-slate-500">
                                <template x-if="status === 'ai_responding'">
                                    <span>AI is responding — please wait.</span>
                                </template>
                                <template x-if="status !== 'ai_responding' && !isMyTurn">
                                    <span>It’s not your turn — input is locked.</span>
                                </template>
                                <template x-if="isMyTurn && status !== 'ai_responding'">
                                    <span>Tip: use “I feel… because I need…”</span>
                                </template>
                            </div>

                            <button
                                type="submit"
                                :disabled="!canWrite || content.trim().length < 10"
                                class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-teal-600 to-blue-600 px-5 py-2.5 text-sm font-semibold text-white
                                       shadow-sm hover:shadow-md active:scale-[0.99] transition
                                       disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Submit
                            </button>
                        </div>
                    </form>
                </section>
            @endif

            <!-- Completed -->
            <section x-show="status === 'completed'" class="mt-6 rounded-2xl border border-slate-200 bg-white/80 p-8 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 ring-1 ring-emerald-200">
                    <svg class="h-7 w-7 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Session completed</h3>
                <p class="mt-2 text-slate-600">Thanks for using MediAItor. We hope this helped you move forward.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block text-sm font-semibold text-teal-700 hover:underline">Return to home</a>
            </section>
        @endif
    </main>

    <script>
        // Simple global helper (used by the waiting screen)
        function copySessionCode(code) {
            if (!navigator.clipboard) {
                // Fallback
                const temp = document.createElement('input');
                temp.value = code;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
                return;
            }
            navigator.clipboard.writeText(code);
        }

        // Copy button in navbar
        (function () {
            const btn = document.getElementById('copyCodeBtn');
            const codeEl = document.getElementById('sessionCode');

            btn?.addEventListener('click', async () => {
                const code = codeEl?.textContent?.trim() || '';
                try {
                    await navigator.clipboard.writeText(code);
                    btn.textContent = 'Copied';
                    setTimeout(() => (btn.textContent = 'Copy'), 900);
                } catch (e) {
                    copySessionCode(code);
                    btn.textContent = 'Copied';
                    setTimeout(() => (btn.textContent = 'Copy'), 900);
                }
            });
        })();

        function mediationRoom() {
            return {
                status: @json($session->status),
                round: {{ $initialRound }},
                messages: @json($messagesJson),
                currentRole: @json($currentParticipant?->role ?? ""),
                pollInterval: null,
                content: "",

                get canWrite() {
                    return this.isMyTurn && this.status !== 'ai_responding' && this.status !== 'completed';
                },

                get turnMessage() {
                    const user1Name = @json($user1?->name ?? "User 1");
                    const user2Name = @json($user2?->name ?? "User 2");

                    switch (this.status) {
                        case 'waiting_for_partner':
                            return 'Waiting for partner to join...';
                        case 'user1_turn':
                            return `Waiting for ${user1Name} to share their perspective...`;
                        case 'user2_turn':
                            return `Waiting for ${user2Name} to share their perspective...`;
                        case 'ai_responding':
                            return 'Dr. Harmony is preparing a response...';
                        case 'completed':
                            return 'Session completed';
                        default:
                            return 'Loading...';
                    }
                },

                get isMyTurn() {
                    if (!this.currentRole) return false;
                    if (this.currentRole === 'user1' && this.status === 'user1_turn') return true;
                    if (this.currentRole === 'user2' && this.status === 'user2_turn') return true;
                    return false;
                },

                get rounds() {
                    const grouped = {};
                    for (const msg of this.messages) {
                        const r = msg.round_number ?? 1;
                        if (!grouped[r]) grouped[r] = [];
                        grouped[r].push(msg);
                    }

                    return Object.keys(grouped)
                        .map(n => Number(n))
                        .sort((a, b) => a - b)
                        .map(n => ({
                            round: n,
                            messages: grouped[n].slice().sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
                        }));
                },

                formatTime(isoString) {
                    const date = new Date(isoString);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                },

                scrollToBottom() {
                    const el = document.getElementById('messages-container');
                    if (!el) return;
                    el.scrollTop = el.scrollHeight;
                },

                async fetchStatus() {
                    try {
                        const prevStatus = this.status;
                        const prevLen = this.messages.length;

                        const response = await fetch('/api/session/{{ $session->code }}/status');
                        const data = await response.json();

                        // Update state
                        const changed = (data.status !== prevStatus) || (data.messages.length !== prevLen);

                        if (changed) {
                            this.status = data.status;
                            this.round = data.round;
                            this.messages = data.messages;

                            // If partner joined, reload so Blade variables ($user2 name etc.) update nicely
                            if (prevStatus === 'waiting_for_partner' && data.status !== 'waiting_for_partner') {
                                window.location.reload();
                                return;
                            }

                            // If new messages, auto-scroll
                            if (data.messages.length > prevLen) {
                                requestAnimationFrame(() => this.scrollToBottom());
                            }
                        }
                    } catch (error) {
                        console.error('Polling error:', error);
                    }
                },

                startPolling() {
                    // initial scroll
                    requestAnimationFrame(() => this.scrollToBottom());
                    // Poll every 3s
                    this.pollInterval = setInterval(() => this.fetchStatus(), 3000);
                },

                stopPolling() {
                    if (this.pollInterval) clearInterval(this.pollInterval);
                }
            };
        }
    </script>
</body>
</html>
