<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediAItor - AI-Powered Conflict Resolution</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50 min-h-screen text-slate-800">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center" aria-hidden="true">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-slate-800">MediAItor</span>
            </a>

            <div class="flex items-center gap-3 sm:gap-4">
                <!-- ✅ Added: Pricing / Premium CTA -->
                <a href="{{ route('pricing') }}"
                   class="hidden sm:inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                    See pricing
                </a>
                <a href="{{ route('pricing') }}"
                   class="sm:hidden text-slate-600 hover:text-slate-800">
                    Pricing
                </a>

                <a href="{{ route('rooms') }}" class="text-slate-600 hover:text-slate-800">Rooms</a>
                @auth
                    <a href="{{ route('memory.index') }}" class="text-slate-600 hover:text-slate-800">Memory</a>
                @endauth

                @auth
                    <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-slate-800">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-600 hover:text-slate-800">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-800">Login</a>
                    <a href="{{ route('register') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    @if ($errors->any())
        <div class="max-w-6xl mx-auto px-4 pt-6">
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    @if ($activeSession ?? false)
        <div class="max-w-6xl mx-auto px-4 pt-6">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="font-medium text-amber-800">You have an active session</p>
                    <p class="text-sm text-amber-600">Code: {{ $activeSession->code }} &bull; {{ ucfirst($activeSession->conflict_type) }}</p>
                </div>
                <a href="{{ route('session.room', $activeSession->code) }}"
                   class="bg-amber-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-amber-700 transition-colors">
                    Rejoin Session
                </a>
            </div>
        </div>
    @endif

    <!-- Hero -->
    <main class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <section class="grid lg:grid-cols-2 gap-10 items-start mb-14">
            <!-- Left: messaging -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-white/70 border border-slate-200 rounded-full px-4 py-2 text-sm text-slate-600 mb-6">
                    <span class="w-2 h-2 rounded-full bg-teal-500" aria-hidden="true"></span>
                    Turn-based, calmer conversations in minutes
                </div>

                <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-5 leading-tight">
                    Resolve conflicts with
                    <span class="bg-gradient-to-r from-teal-600 to-blue-600 bg-clip-text text-transparent">AI-guided</span>
                    mediation
                </h1>

                <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto lg:mx-0 mb-7">
                    MediAItor helps two people slow down, take turns, and understand each other—then Dr. Harmony offers research-inspired prompts and next steps.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start mb-7">
                    <button id="demoBtn"
                            type="button"
                            class="bg-white text-slate-700 px-6 py-3 rounded-xl text-base font-semibold border-2 border-slate-200 hover:border-teal-300 hover:text-teal-700 transition-all">
                        See a demo
                    </button>

                    <a href="#how-it-works"
                       class="text-slate-600 hover:text-slate-800 px-2 py-3 text-base font-semibold">
                        How it works →
                    </a>

                    <!-- ✅ Added: Secondary CTA in hero (optional but nice) -->
                    <a href="{{ route('pricing') }}"
                       class="text-slate-600 hover:text-slate-800 px-2 py-3 text-base font-semibold">
                        Buy Premium →
                    </a>
                </div>

                <!-- Quick trust strip -->
                <div class="grid sm:grid-cols-3 gap-3">
                    <div class="bg-white/70 border border-slate-200 rounded-2xl p-4">
                        <p class="text-sm font-semibold">Two-person rooms</p>
                        <p class="text-sm text-slate-600">Designed for 1:1 conversations.</p>
                    </div>
                    <div class="bg-white/70 border border-slate-200 rounded-2xl p-4">
                        <p class="text-sm font-semibold">Turn-taking</p>
                        <p class="text-sm text-slate-600">One person writes at a time.</p>
                    </div>
                    <div class="bg-white/70 border border-slate-200 rounded-2xl p-4">
                        <p class="text-sm font-semibold">Delete anytime</p>
                        <p class="text-sm text-slate-600">End + clear a session when done.</p>
                    </div>
                </div>
            </div>

            <!-- Right: inline create/join -->
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-7">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-bold text-slate-800">Start now</h2>
                    <div class="text-xs text-slate-500">
                        Example code:
                        <span class="font-mono bg-slate-100 px-2 py-1 rounded">A7K4F2</span>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <!-- Create (DIRECT POST to session.store) -->
                    <div class="border border-slate-200 rounded-2xl p-4">
                        <h3 class="font-semibold text-slate-800 mb-2">Create a session</h3>
                        <p class="text-sm text-slate-600 mb-3">Get a code and share it with the other person.</p>

                        <form action="{{ route('session.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="create_name" class="block text-sm font-medium text-slate-700 mb-1">
                                    Your name
                                </label>
                                <input id="create_name"
                                       name="name"
                                       type="text"
                                       required
                                       placeholder="e.g., Rayhan"
                                       class="w-full rounded-xl border-slate-200 focus:border-teal-400 focus:ring-teal-400"
                                       autocomplete="name">
                            </div>

                            <button type="submit"
                                    class="w-full bg-gradient-to-r from-teal-600 to-blue-600 text-white px-4 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-teal-500/20 transition-all">
                                Create &amp; get code
                            </button>
                        </form>
                    </div>

                    <!-- Join (DIRECT POST to session.doJoin) -->
                    <div class="border border-slate-200 rounded-2xl p-4">
                        <h3 class="font-semibold text-slate-800 mb-2">Join a session</h3>
                        <p class="text-sm text-slate-600 mb-3">Enter the code you received.</p>

                        <form action="{{ route('session.doJoin') }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="join_name" class="block text-sm font-medium text-slate-700 mb-1">
                                    Your name
                                </label>
                                <input id="join_name"
                                       name="name"
                                       type="text"
                                       required
                                       placeholder="e.g., Sam"
                                       class="w-full rounded-xl border-slate-200 focus:border-teal-400 focus:ring-teal-400"
                                       autocomplete="name">
                            </div>

                            <div>
                                <label for="join_code" class="block text-sm font-medium text-slate-700 mb-1">
                                    Session code
                                </label>
                                <input id="join_code"
                                       name="code"
                                       type="text"
                                       required
                                       placeholder="Enter code"
                                       class="w-full rounded-xl border-slate-200 focus:border-teal-400 focus:ring-teal-400 font-mono tracking-wider uppercase"
                                       autocomplete="one-time-code"
                                       inputmode="text">
                            </div>

                            <button type="submit"
                                    class="w-full bg-white text-slate-700 px-4 py-3 rounded-xl font-semibold border-2 border-slate-200 hover:border-teal-300 hover:text-teal-700 transition-all">
                                Join session
                            </button>
                        </form>
                    </div>
                </div>

                <!-- ✅ Added: small premium upsell strip (optional, but converts well) -->
                <div class="mt-4 bg-gradient-to-r from-teal-50 to-blue-50 border border-slate-200 rounded-2xl p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Need a group room?</p>
                            <p class="text-sm text-slate-600">
                                Premium supports group chats, round-robin turns, and action-plan summaries.
                            </p>
                        </div>
                        <a href="{{ route('pricing') }}"
                           class="shrink-0 inline-flex items-center justify-center bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-slate-800 transition">
                            Upgrade
                        </a>
                    </div>
                </div>

                <!-- Rules of the room -->
                <div class="mt-5 bg-slate-50 border border-slate-200 rounded-2xl p-4">
                    <h4 class="font-semibold text-slate-800 mb-2">Rules of the room</h4>
                    <ul class="text-sm text-slate-600 space-y-2">
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-teal-500 flex-shrink-0" aria-hidden="true"></span>
                            One person writes at a time (no interruptions).
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" aria-hidden="true"></span>
                            After both share, Dr. Harmony reflects and suggests next steps.
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-purple-500 flex-shrink-0" aria-hidden="true"></span>
                            Focus on feelings, needs, and actionable requests.
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Privacy strip -->
        <section class="bg-white/80 border border-slate-200 rounded-2xl p-6 mb-14">
            <div class="grid md:grid-cols-3 gap-5">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0" aria-hidden="true">
                        <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21a7 7 0 10-14 0"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Minimal friction</p>
                        <p class="text-sm text-slate-600">Just names + code to start. Keep it simple.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0" aria-hidden="true">
                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m6 4H6a2 2 0 01-2-2v-6a2 2 0 012-2h12a2 2 0 012 2v6a2 2 0 01-2 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 10-8 0v4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Session control</p>
                        <p class="text-sm text-slate-600">End the room when resolved. Clear it anytime.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0" aria-hidden="true">
                        <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Calm by design</p>
                        <p class="text-sm text-slate-600">Turn-taking reduces escalation and noise.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="bg-white rounded-2xl shadow-xl p-8 mb-14">
            <div class="flex items-center justify-between flex-wrap gap-3 mb-8">
                <h2 class="text-2xl font-bold text-slate-800">How it works</h2>
                <span class="text-sm text-slate-500">Two people • One room • Turn-by-turn</span>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4" aria-hidden="true">
                        <span class="text-2xl font-bold text-teal-600">1</span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">Create session</h3>
                    <p class="text-slate-600 text-sm">Start a room and get a unique code.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4" aria-hidden="true">
                        <span class="text-2xl font-bold text-blue-600">2</span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">Invite partner</h3>
                    <p class="text-slate-600 text-sm">Share the code with one person.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4" aria-hidden="true">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">Take turns</h3>
                    <p class="text-slate-600 text-sm">One message each—no interruptions.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4" aria-hidden="true">
                        <span class="text-2xl font-bold text-pink-600">4</span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">AI mediation</h3>
                    <p class="text-slate-600 text-sm">Dr. Harmony reflects and suggests next steps.</p>
                </div>
            </div>
        </section>

        <!-- Meet Dr. Harmony -->
        <section class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl shadow-xl p-8 text-white mb-14">
            <div class="flex items-start gap-6">
                <div class="w-20 h-20 bg-gradient-to-br from-teal-400 to-blue-500 rounded-2xl flex items-center justify-center flex-shrink-0" aria-hidden="true">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>

                <div>
                    <h2 class="text-2xl font-bold mb-2">Meet Dr. Harmony</h2>
                    <p class="text-slate-300 mb-4">Your AI mediator</p>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-teal-500/20 text-teal-200 px-3 py-1 rounded-full text-sm">Gottman-inspired</span>
                        <span class="bg-blue-500/20 text-blue-200 px-3 py-1 rounded-full text-sm">NVC-style prompts</span>
                        <span class="bg-purple-500/20 text-purple-200 px-3 py-1 rounded-full text-sm">EFT-informed reflection</span>
                        <span class="bg-pink-500/20 text-pink-200 px-3 py-1 rounded-full text-sm">Negotiation framing</span>
                    </div>

                    <p class="text-slate-300 text-sm">
                        Dr. Harmony helps reframe blame into needs, highlights common ground, and suggests clear, doable requests—so you can move forward without escalating.
                    </p>
                </div>
            </div>
        </section>

        <!-- What it is / isn't -->
        <section class="bg-white rounded-2xl shadow-xl p-8 mb-14">
            <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">What MediAItor is (and isn’t)</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="border border-slate-200 rounded-2xl p-6">
                    <h3 class="font-semibold text-slate-800 mb-3">It is</h3>
                    <ul class="text-sm text-slate-600 space-y-2">
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-teal-500 flex-shrink-0" aria-hidden="true"></span>
                            A structured way to take turns and stay constructive
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-teal-500 flex-shrink-0" aria-hidden="true"></span>
                            A reflection tool that turns conflict into clear requests
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-teal-500 flex-shrink-0" aria-hidden="true"></span>
                            Best for “we’re stuck” conversations, not quick arguments
                        </li>
                    </ul>
                </div>

                <div class="border border-slate-200 rounded-2xl p-6">
                    <h3 class="font-semibold text-slate-800 mb-3">It isn’t</h3>
                    <ul class="text-sm text-slate-600 space-y-2">
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-rose-500 flex-shrink-0" aria-hidden="true"></span>
                            A replacement for therapy, legal help, or emergency services
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-rose-500 flex-shrink-0" aria-hidden="true"></span>
                            Suitable for situations involving abuse, coercion, or immediate danger
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full bg-rose-500 flex-shrink-0" aria-hidden="true"></span>
                            A tool that “decides who is right”
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Research Backed (softened claims) -->
        <section class="text-center mb-6">
            <h3 class="text-lg font-semibold text-slate-700 mb-3">Powered by proven communication ideas</h3>
            <div class="flex flex-wrap justify-center gap-3 text-slate-600 text-sm">
                <span class="bg-white/70 border border-slate-200 rounded-full px-4 py-2">Turn-taking to reduce escalation</span>
                <span class="bg-white/70 border border-slate-200 rounded-full px-4 py-2">Needs-based reframing (NVC-style)</span>
                <span class="bg-white/70 border border-slate-200 rounded-full px-4 py-2">Focus on repair + forward steps</span>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8 mt-10">
        <div class="max-w-6xl mx-auto px-4 text-center text-slate-500">
            <p>Built for Hackathon 2026 | Powered by AI and Coffee</p>
        </div>
    </footer>

    <!-- Demo Modal -->
    <div id="demoModal"
         class="fixed inset-0 z-50 hidden"
         role="dialog"
         aria-modal="true"
         aria-labelledby="demoTitle">
        <!-- Overlay -->
        <div id="demoOverlay" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <!-- Panel -->
        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                    <h3 id="demoTitle" class="text-lg font-bold text-slate-800">Demo: a calm, turn-based exchange</h3>
                    <button id="demoClose"
                            type="button"
                            class="text-slate-500 hover:text-slate-800 rounded-lg px-2 py-1"
                            aria-label="Close demo">
                        ✕
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                        <p class="text-xs text-slate-500 mb-1">Person A</p>
                        <p class="text-slate-700">
                            “When plans change last minute, I feel anxious because I need some predictability.”
                        </p>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                        <p class="text-xs text-slate-500 mb-1">Person B</p>
                        <p class="text-slate-700">
                            “I didn’t realize it affected you that much. I feel pressured when I’m expected to decide early.”
                        </p>
                    </div>

                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 text-white rounded-2xl p-4">
                        <p class="text-xs text-slate-300 mb-1">Dr. Harmony</p>
                        <p class="text-slate-100">
                            “I’m hearing a need for predictability (A) and a need for flexibility (B). Try a small agreement:
                            share a ‘maybe’ plan early, then confirm by a specific time. What time feels fair for both?”
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-end pt-2">
                        <button id="demoStartSession"
                                type="button"
                                class="bg-gradient-to-r from-teal-600 to-blue-600 text-white px-5 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-teal-500/20 transition-all">
                            Start a session
                        </button>

                        <button id="demoCloseBottom"
                                type="button"
                                class="bg-white text-slate-700 px-5 py-3 rounded-xl font-semibold border-2 border-slate-200 hover:border-teal-300 hover:text-teal-700 transition-all">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Small inline JS for modal -->
    <script>
        (function () {
            const btn = document.getElementById('demoBtn');
            const modal = document.getElementById('demoModal');
            const overlay = document.getElementById('demoOverlay');
            const closeTop = document.getElementById('demoClose');
            const closeBottom = document.getElementById('demoCloseBottom');
            const start = document.getElementById('demoStartSession');

            function openModal() {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                closeTop.focus();
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                btn.focus();
            }

            btn?.addEventListener('click', openModal);
            overlay?.addEventListener('click', closeModal);
            closeTop?.addEventListener('click', closeModal);
            closeBottom?.addEventListener('click', closeModal);

            start?.addEventListener('click', function () {
                // Direct users to your Create SESSION PAGE (or change to POST flow if you want)
                window.location.href = "{{ route('session.create') }}";
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
</body>
</html>
