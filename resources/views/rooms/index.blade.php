<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms - MediAItor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-slate-800 bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50">
    <!-- soft background decor -->
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-teal-400/15 blur-3xl"></div>
        <div class="absolute top-40 -left-28 h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 h-72 w-72 rounded-full bg-slate-900/5 blur-3xl"></div>
    </div>

    <!-- NAV -->
    <header class="sticky top-0 z-50">
        <nav class="border-b border-slate-200/70 bg-white/70 backdrop-blur-xl">
            <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
                <a href="{{ route('home') }}" class="group flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-teal-500 to-blue-600 flex items-center justify-center shadow-sm ring-1 ring-white/50">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-slate-950">MediAItor</div>
                        <div class="hidden sm:block text-xs text-slate-500">Calm, guided conflict resolution</div>
                    </div>
                </a>

                <div class="flex items-center gap-2 sm:gap-4">
                    <a href="{{ route('pricing') }}"
                       class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">
                        Pricing
                    </a>

                    <a href="{{ route('rooms') }}"
                       class="text-sm font-semibold text-slate-900">
                        Rooms
                    </a>

                    <div class="hidden sm:block h-6 w-px bg-slate-200"></div>

                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">
                            Dashboard
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10 sm:py-12">
        <!-- Top header card -->
        <section class="rounded-3xl border border-slate-200/70 bg-white/70 backdrop-blur-xl shadow-sm">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Your rooms</h1>

                            @if($isPremium)
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 1l2.245 5.16L18 6.75l-4.5 3.9L14.49 17 10 13.95 5.51 17l.99-6.35L2 6.75l5.755-.59L10 1z" clip-rule="evenodd" />
                                    </svg>
                                    Premium
                                </span>
                            @endif
                        </div>

                        <p class="mt-2 text-sm sm:text-base text-slate-600">
                            Active rooms:
                            <span class="font-semibold text-slate-900">{{ $activeCount }}</span>
                            <span class="text-slate-400">/</span>
                            <span class="font-semibold text-slate-900">{{ $activeLimit }}</span>

                            @if(!$isPremium && $isLoggedIn)
                                <a href="{{ route('pricing') }}"
                                   class="ml-3 inline-flex items-center gap-1 text-sm font-semibold text-teal-700 hover:text-teal-800 hover:underline">
                                    Upgrade
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.293 4.293a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 11-1.414-1.414L15.586 11H2a1 1 0 110-2h13.586l-3.293-3.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endif
                        </p>

                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 ring-1 ring-slate-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Active = ongoing
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 ring-1 ring-slate-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                Completed = finished
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <a href="{{ route('session.create') }}"
                           class="group inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-teal-600 to-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:shadow-md hover:shadow-teal-500/20 transition">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                            </svg>
                            Create room
                        </a>

                        <a href="{{ route('session.join') }}"
                           class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-white/80 px-5 py-3 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 hover:ring-teal-200 hover:text-teal-700 transition">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 012-2h8a2 2 0 012 2v3a1 1 0 11-2 0V3H6v14h8v-3a1 1 0 112 0v3a2 2 0 01-2 2H6a2 2 0 01-2-2V3zm9.293 6.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L14.586 12H9a1 1 0 110-2h5.586l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Join room
                        </a>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <svg class="h-5 w-5 text-rose-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-8-4a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1zm0 8a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 14z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <p class="text-sm font-medium">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Rooms -->
        <section class="mt-8">
            @if($rooms->isEmpty())
                <div class="rounded-3xl border border-slate-200/70 bg-white/70 backdrop-blur-xl p-10 text-center shadow-sm">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900/5 ring-1 ring-slate-200">
                        <svg class="h-7 w-7 text-slate-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M2 5a2 2 0 012-2h3.5a1 1 0 01.8.4l.9 1.2H16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"/>
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-slate-900">No rooms yet</h2>
                    <p class="mt-2 text-slate-600">Create a room or join with a 6-character code.</p>
                    <div class="mt-6 flex flex-col sm:flex-row justify-center gap-3">
                        <a href="{{ route('session.create') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Create room
                        </a>
                        <a href="{{ route('session.join') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 hover:ring-teal-200 hover:text-teal-700 transition">
                            Join room
                        </a>
                    </div>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($rooms as $room)
                        <a href="{{ route('session.room', $room['code']) }}"
                           class="group relative overflow-hidden rounded-3xl border border-slate-200/70 bg-white/70 backdrop-blur-xl p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <!-- hover shine -->
                            <div class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition">
                                <div class="absolute -top-24 -right-24 h-56 w-56 rounded-full bg-teal-400/10 blur-2xl"></div>
                            </div>

                            <div class="relative flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs font-medium text-slate-500">Code</div>
                                    <div class="mt-1 font-mono text-lg font-bold tracking-[0.25em] text-slate-900">
                                        {{ $room['code'] }}
                                    </div>
                                </div>

                                <div class="text-right">
                                    @if($room['is_active'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Completed
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="relative mt-4 flex flex-wrap items-center gap-2 text-xs">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700 ring-1 ring-slate-200 capitalize">
                                    {{ $room['conflict_type'] }}
                                </span>

                                <span class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700 ring-1 ring-slate-200 capitalize">
                                    {{ str_replace('_', ' ', $room['status']) }}
                                </span>

                                @if($room['role'])
                                    <span class="rounded-full bg-teal-50 px-2.5 py-1 font-semibold text-teal-700 ring-1 ring-teal-200 capitalize">
                                        {{ $room['role'] }}
                                    </span>
                                @endif
                            </div>

                            @if($room['name'])
                                <div class="relative mt-4 rounded-2xl bg-white/60 px-3 py-2 ring-1 ring-slate-200">
                                    <div class="text-xs text-slate-500">You</div>
                                    <div class="text-sm font-semibold text-slate-800">{{ $room['name'] }}</div>
                                </div>
                            @endif

                            <div class="relative mt-5 inline-flex items-center gap-2 text-sm font-semibold text-teal-700">
                                Open room
                                <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.293 4.293a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 11-1.414-1.414L15.586 11H2a1 1 0 110-2h13.586l-3.293-3.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <footer class="mt-10 text-center text-xs text-slate-500">
            Tip: Share the room code with the other person to start the session quickly.
        </footer>
    </main>
</body>
</html>
