<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Details - MediAItor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('memory.index') }}" class="flex items-center gap-2 text-slate-600 hover:text-slate-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to memories
            </a>
            <form method="POST" action="{{ route('memory.destroy', $memory) }}" onsubmit="return confirm('Delete this memory?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-rose-600 hover:text-rose-800 text-sm font-medium">
                    Delete memory
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 mb-2">
                        {{ $memory->partner_name ? 'Session with ' . $memory->partner_name : 'Mediation Session' }}
                    </h1>
                    <div class="flex items-center gap-3 text-sm text-slate-500">
                        <span class="capitalize">{{ $memory->conflict_type }}</span>
                        <span>&bull;</span>
                        <span>{{ $memory->created_at->format('F j, Y \a\t g:i A') }}</span>
                        <span>&bull;</span>
                        <span class="bg-violet-50 text-violet-600 px-2 py-0.5 rounded">
                            {{ $memory->save_level === 'all' ? 'Full transcript saved' : 'Summary only' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Summary</h2>
            <div class="prose prose-slate max-w-none">
                <p class="whitespace-pre-wrap text-slate-700">{{ $memory->summary }}</p>
            </div>
        </div>

        <!-- Insights -->
        @if(!empty($memory->insights))
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                @if(!empty($memory->getThemes()))
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-4">Themes</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($memory->getThemes() as $theme)
                                <span class="bg-violet-50 text-violet-700 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $theme }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($memory->getPatterns()))
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-4">Patterns</h2>
                        <ul class="space-y-2">
                            @foreach($memory->getPatterns() as $pattern)
                                <li class="flex items-start gap-2 text-slate-600">
                                    <span class="text-violet-500 mt-1">&bull;</span>
                                    {{ $pattern }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        <!-- Full Transcript -->
        @if($memory->hasTranscript())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Full Transcript</h2>
                <div class="bg-slate-50 rounded-xl p-4 max-h-[500px] overflow-y-auto">
                    <pre class="whitespace-pre-wrap text-sm text-slate-700 font-mono">{{ $memory->transcript }}</pre>
                </div>
            </div>
        @endif
    </main>
</body>
</html>
