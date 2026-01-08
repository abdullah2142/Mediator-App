<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms - MediAItor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50 min-h-screen text-slate-800">
    <nav class="bg-white/80 backdrop-blur-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center" aria-hidden="true">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-slate-800">MediAItor</span>
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ route('pricing') }}" class="text-slate-600 hover:text-slate-800">Pricing</a>
                <a href="{{ route('rooms') }}" class="text-slate-900 font-semibold">Rooms</a>

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

    <main class="max-w-6xl mx-auto px-4 py-12">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Your rooms</h1>
                <p class="mt-1 text-slate-600">
                    Active rooms: <span class="font-semibold text-slate-800">{{ $activeCount }}</span>/<span class="font-semibold text-slate-800">{{ $activeLimit }}</span>
                    @if($isPremium)
                        <span class="ml-2 inline-flex items-center rounded-full bg-slate-900 px-2 py-0.5 text-xs font-semibold text-white">Premium</span>
                    @elseif($isLoggedIn)
                        <a href="{{ route('pricing') }}" class="ml-2 text-sm font-semibold text-teal-700 hover:underline">Upgrade</a>
                    @endif
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('session.create') }}"
                   class="inline-flex items-center justify-center bg-gradient-to-r from-teal-600 to-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-teal-500/20 transition-all">
                    Create room
                </a>
                <a href="{{ route('session.join') }}"
                   class="inline-flex items-center justify-center bg-white text-slate-700 px-5 py-2.5 rounded-xl font-semibold border-2 border-slate-200 hover:border-teal-300 hover:text-teal-700 transition-all">
                    Join room
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="mt-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="mt-8">
            @if($rooms->isEmpty())
                <div class="bg-white/80 border border-slate-200 rounded-2xl p-8 text-center shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">No rooms yet</h2>
                    <p class="mt-2 text-slate-600">Create a room or join with a 6-character code.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($rooms as $room)
                        <a href="{{ route('session.room', $room['code']) }}"
                           class="group bg-white/80 border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs text-slate-500">Code</div>
                                    <div class="font-mono text-lg font-bold tracking-widest text-slate-900">{{ $room['code'] }}</div>
                                </div>

                                <div class="text-right">
                                    @if($room['is_active'])
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                            Completed
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                                <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700 ring-1 ring-slate-200 capitalize">
                                    {{ $room['conflict_type'] }}
                                </span>
                                <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700 ring-1 ring-slate-200">
                                    {{ str_replace('_', ' ', $room['status']) }}
                                </span>
                                @if($room['role'])
                                    <span class="rounded-full bg-teal-50 px-2 py-1 font-semibold text-teal-700 ring-1 ring-teal-200">
                                        {{ $room['role'] }}
                                    </span>
                                @endif
                            </div>

                            @if($room['name'])
                                <div class="mt-3 text-sm text-slate-600">
                                    You: <span class="font-semibold text-slate-800">{{ $room['name'] }}</span>
                                </div>
                            @endif

                            <div class="mt-4 text-sm font-semibold text-teal-700 group-hover:underline">
                                Open room →
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</body>
</html>

