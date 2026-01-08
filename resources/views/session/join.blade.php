<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Session - MediAItor</title>
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
        </div>
    </nav>

    <main class="max-w-md mx-auto px-4 py-16">
        @if ($activeSession)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-center justify-between">
                <div>
                    <p class="font-medium text-amber-800">You have an active session</p>
                    <p class="text-sm text-amber-600">Code: {{ $activeSession->code }}</p>
                </div>
                <a href="{{ route('session.room', $activeSession->code) }}"
                   class="bg-amber-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-amber-700 transition-colors">
                    Rejoin
                </a>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Join a Session</h1>
            <p class="text-slate-600 mb-6">Enter the 6-character code shared with you.</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('session.doJoin') }}">
                @csrf

                <div class="mb-6">
                    <label for="code" class="block text-sm font-medium text-slate-700 mb-2">Session Code</label>
                    <input type="text" id="code" name="code" required maxlength="6"
                           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-center text-2xl tracking-widest uppercase"
                           placeholder="ABC123"
                           value="{{ old('code') }}">
                </div>

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Your Name</label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="Enter your display name"
                           value="{{ old('name') }}">
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-teal-600 to-blue-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                    Join Session
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-slate-500 text-sm">
                    Need to start fresh? <a href="{{ route('session.create') }}" class="text-teal-600 hover:underline">Create a session</a>
                </p>
            </div>
        </div>
    </main>
</body>
</html>
