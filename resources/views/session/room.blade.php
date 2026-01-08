<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Session {{ $session->code }} - MediAItor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-slate-800">MediAItor</span>
            </a>

            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-3 py-1.5 rounded-lg">
                    <span class="text-slate-500 text-sm">Code:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $session->code }}</span>
                </div>
                <div class="bg-{{ $session->conflict_type === 'relationship' ? 'pink' : ($session->conflict_type === 'workplace' ? 'blue' : 'teal') }}-100 px-3 py-1.5 rounded-lg">
                    <span class="text-slate-700 text-sm capitalize">{{ $session->conflict_type }}</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-8" x-data="mediationRoom()" x-init="startPolling()">
        <!-- Dr. Harmony Badge -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl p-4 mb-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-teal-400 to-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <div class="text-white">
                <h2 class="font-bold">Dr. Harmony - AI Mediator</h2>
                <div class="flex gap-2 text-xs mt-1">
                    <span class="bg-teal-500/20 text-teal-300 px-2 py-0.5 rounded">Gottman</span>
                    <span class="bg-blue-500/20 text-blue-300 px-2 py-0.5 rounded">NVC</span>
                    <span class="bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded">EFT</span>
                </div>
            </div>
            <div class="ml-auto text-white text-sm">
                Round <span class="font-bold" x-text="round">{{ $session->messages->where('type', 'ai')->count() + 1 }}</span>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6 flex items-center justify-between"
             :class="{ 'border-2 border-teal-500': isMyTurn }">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full animate-pulse"
                     :class="status === 'completed' ? 'bg-slate-400' : 'bg-green-500'"></div>
                <span class="font-medium text-slate-700" x-text="turnMessage">
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
                </span>
            </div>
            @if(!$session->isCompleted() && $currentParticipant)
                <form method="POST" action="{{ route('session.end', $session->code) }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">End Session</button>
                </form>
            @endif
        </div>

        <!-- Waiting for Partner -->
        @if($session->status === 'waiting_for_partner')
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-teal-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Waiting for your partner</h3>
                <p class="text-slate-600 mb-4">Share this code with the other person:</p>
                <div class="bg-slate-100 rounded-lg p-4 inline-block">
                    <span class="text-3xl font-mono font-bold text-slate-800 tracking-widest">{{ $session->code }}</span>
                </div>
                <p class="text-slate-500 text-sm mt-4">The page will update automatically when they join.</p>
            </div>
        @else
            <!-- Messages Area -->
            <div class="space-y-6 mb-6" id="messages-container">
                <template x-for="(roundMessages, roundNum) in groupedMessages" :key="roundNum">
                    <div class="space-y-4">
                        <!-- Round Header -->
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <div class="flex-1 h-px bg-slate-200"></div>
                            <span>Round <span x-text="roundNum"></span></span>
                            <div class="flex-1 h-px bg-slate-200"></div>
                        </div>

                        <!-- User Messages -->
                        <template x-for="msg in roundMessages.filter(m => m.type === 'user')" :key="msg.id">
                            <div class="bg-white rounded-xl shadow-lg p-6"
                                 :class="msg.participant_role === 'user1' ? 'border-l-4 border-teal-500' : 'border-l-4 border-blue-500'">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                         :class="msg.participant_role === 'user1' ? 'bg-teal-500' : 'bg-blue-500'"
                                         x-text="msg.participant_name.charAt(0).toUpperCase()"></div>
                                    <span class="font-semibold text-slate-800" x-text="msg.participant_name"></span>
                                    <span class="text-slate-400 text-sm" x-text="formatTime(msg.created_at)"></span>
                                </div>
                                <p class="text-slate-700 whitespace-pre-wrap" x-text="msg.content"></p>
                            </div>
                        </template>

                        <!-- AI Response -->
                        <template x-for="msg in roundMessages.filter(m => m.type === 'ai')" :key="msg.id">
                            <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl shadow-lg p-6 text-white">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-teal-400 to-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                    </div>
                                    <span class="font-semibold">Dr. Harmony</span>
                                    <span class="text-slate-400 text-sm" x-text="formatTime(msg.created_at)"></span>
                                </div>
                                <div class="text-slate-200 whitespace-pre-wrap leading-relaxed" x-text="msg.content"></div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- AI Loading State -->
                <div x-show="status === 'ai_responding'" class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl shadow-lg p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-teal-400 to-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <div class="text-white">
                            <span class="font-semibold">Dr. Harmony</span>
                            <span class="text-slate-400 ml-2">is thinking...</span>
                        </div>
                        <div class="ml-auto flex gap-1">
                            <div class="w-2 h-2 bg-teal-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                            <div class="w-2 h-2 bg-teal-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-teal-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            @if($currentParticipant && !$session->isCompleted())
                <div class="bg-white rounded-xl shadow-lg p-6"
                     x-show="isMyTurn && status !== 'ai_responding'">
                    <form method="POST" action="{{ route('session.message', $session->code) }}">
                        @csrf
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Share your perspective
                        </label>
                        <textarea name="content" rows="4" required minlength="10" maxlength="5000"
                                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none"
                                  placeholder="Take your time to express your feelings and perspective on the situation..."></textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-between items-center mt-4">
                            <p class="text-slate-500 text-sm">Minimum 10 characters</p>
                            <button type="submit"
                                    class="bg-gradient-to-r from-teal-600 to-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Waiting for other user -->
                <div class="bg-white rounded-xl shadow-lg p-6 text-center"
                     x-show="!isMyTurn && status !== 'ai_responding' && status !== 'completed'">
                    <p class="text-slate-600">Waiting for the other participant to share their perspective...</p>
                </div>
            @endif

            <!-- Session Completed -->
            <div x-show="status === 'completed'" class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Session Completed</h3>
                <p class="text-slate-600 mb-4">Thank you for using MediAItor. We hope this session was helpful.</p>
                <a href="{{ route('home') }}" class="text-teal-600 hover:underline">Return to Home</a>
            </div>
        @endif
    </main>

    <script>
        function mediationRoom() {
            return {
                status: '{{ $session->status }}',
                round: {{ $session->messages->where('type', 'ai')->count() + 1 }},
                messages: @json($messagesJson),
                currentRole: '{{ $currentParticipant?->role ?? "" }}',
                pollInterval: null,

                get turnMessage() {
                    const user1Name = '{{ $user1?->name ?? "User 1" }}';
                    const user2Name = '{{ $user2?->name ?? "User 2" }}';

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

                get groupedMessages() {
                    const grouped = {};
                    this.messages.forEach(msg => {
                        if (!grouped[msg.round_number]) {
                            grouped[msg.round_number] = [];
                        }
                        grouped[msg.round_number].push(msg);
                    });
                    return grouped;
                },

                formatTime(isoString) {
                    const date = new Date(isoString);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                },

                async fetchStatus() {
                    try {
                        const response = await fetch('/api/session/{{ $session->code }}/status');
                        const data = await response.json();

                        if (data.status !== this.status || data.messages.length !== this.messages.length) {
                            this.status = data.status;
                            this.round = data.round;
                            this.messages = data.messages;

                            // If status changed significantly, reload the page
                            if (data.status === 'user1_turn' && this.status === 'waiting_for_partner') {
                                window.location.reload();
                            }
                        }
                    } catch (error) {
                        console.error('Polling error:', error);
                    }
                },

                startPolling() {
                    // Poll every 3 seconds
                    this.pollInterval = setInterval(() => this.fetchStatus(), 3000);
                },

                stopPolling() {
                    if (this.pollInterval) {
                        clearInterval(this.pollInterval);
                    }
                }
            };
        }
    </script>
</body>
</html>
