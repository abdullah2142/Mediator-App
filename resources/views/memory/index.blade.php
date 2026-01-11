<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Harmony's Memory - MediAItor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-slate-800">MediAItor</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('rooms') }}" class="text-slate-600 hover:text-slate-800">Rooms</a>
                <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-slate-800">Dashboard</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Dr. Harmony's Memory</h1>
                    <p class="text-slate-500">{{ $totalSessions }} sessions remembered</p>
                </div>
            </div>

            @if($memories->isNotEmpty())
                <form method="POST" action="{{ route('memory.destroyAll') }}" onsubmit="return confirm('Are you sure you want to delete all memories? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-600 hover:text-rose-800 text-sm font-medium">
                        Clear all memories
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Settings Panel -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Memory Settings -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">Memory Settings</h2>

                    <form method="POST" action="{{ route('memory.settings') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="memory_enabled" value="0">
                                <input type="checkbox"
                                       name="memory_enabled"
                                       value="1"
                                       {{ $settings->memory_enabled ? 'checked' : '' }}
                                       class="w-5 h-5 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                <span class="font-medium text-slate-700">Enable memory</span>
                            </label>
                            <p class="text-sm text-slate-500 mt-1 ml-8">Allow Dr. Harmony to remember your sessions</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Default save level</label>
                            <select name="default_save_level" class="w-full rounded-xl border-slate-300 focus:border-violet-500 focus:ring-violet-500">
                                <option value="all" {{ $settings->default_save_level === 'all' ? 'selected' : '' }}>Everything (full transcript)</option>
                                <option value="summary" {{ $settings->default_save_level === 'summary' ? 'selected' : '' }}>Summary only</option>
                                <option value="none" {{ $settings->default_save_level === 'none' ? 'selected' : '' }}>Don't save by default</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-violet-600 text-white py-2 rounded-xl font-medium hover:bg-violet-700 transition">
                            Save settings
                        </button>
                    </form>
                </div>

                <!-- Stats -->
                @if($topThemes->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-4">Recurring Themes</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($topThemes as $theme => $count)
                                <span class="bg-violet-50 text-violet-700 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $theme }} ({{ $count }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- By Conflict Type -->
                @if($byConflictType->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-4">By Conflict Type</h2>
                        <div class="space-y-2">
                            @foreach($byConflictType as $type => $items)
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600 capitalize">{{ $type }}</span>
                                    <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded-lg text-sm font-medium">
                                        {{ $items->count() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Memories List -->
            <div class="lg:col-span-2">
                @if($memories->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
                        <div class="w-16 h-16 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">No memories yet</h3>
                        <p class="text-slate-600 mb-4">Complete a mediation session and choose to save it to build your memory history.</p>
                        <a href="{{ route('session.create') }}" class="inline-block bg-violet-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-violet-700 transition">
                            Start a session
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($memories as $memory)
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:border-violet-200 transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-semibold text-slate-800">
                                                {{ $memory->partner_name ? 'With ' . $memory->partner_name : 'Session' }}
                                            </span>
                                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs capitalize">
                                                {{ $memory->conflict_type }}
                                            </span>
                                            <span class="bg-violet-50 text-violet-600 px-2 py-0.5 rounded text-xs">
                                                {{ $memory->save_level === 'all' ? 'Full' : 'Summary' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-slate-500">{{ $memory->created_at->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('memory.show', $memory) }}" class="text-violet-600 hover:text-violet-800 text-sm font-medium">
                                            View
                                        </a>
                                        <form method="POST" action="{{ route('memory.destroy', $memory) }}" class="inline" onsubmit="return confirm('Delete this memory?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <p class="text-slate-600 text-sm line-clamp-3">{{ Str::limit($memory->summary, 250) }}</p>

                                @if(!empty($memory->getThemes()))
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach(array_slice($memory->getThemes(), 0, 4) as $theme)
                                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs">{{ $theme }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
