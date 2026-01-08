<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediAItor - AI-Powered Conflict Resolution</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-slate-800">MediAItor</span>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-slate-800">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-600 hover:text-slate-800">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-800">Login</a>
                    <a href="{{ route('register') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="max-w-6xl mx-auto px-4 py-16">
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-slate-800 mb-6">
                Resolve Conflicts with
                <span class="bg-gradient-to-r from-teal-600 to-blue-600 bg-clip-text text-transparent">AI-Powered</span>
                Mediation
            </h1>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto mb-8">
                MediAItor uses evidence-based therapeutic approaches to help you and your partner navigate difficult conversations constructively.
            </p>

            <!-- CTA Buttons -->
            <div class="flex justify-center gap-4 mb-12">
                <a href="{{ route('session.create') }}" class="bg-gradient-to-r from-teal-600 to-blue-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:shadow-lg hover:shadow-teal-500/30 transition-all">
                    Start a Session
                </a>
                <a href="{{ route('session.join') }}" class="bg-white text-slate-700 px-8 py-4 rounded-xl text-lg font-semibold border-2 border-slate-200 hover:border-teal-300 hover:text-teal-700 transition-all">
                    Join a Session
                </a>
            </div>
        </div>

        <!-- How It Works -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-16">
            <h2 class="text-2xl font-bold text-slate-800 text-center mb-8">How It Works</h2>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-teal-600">1</span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">Create Session</h3>
                    <p class="text-slate-600 text-sm">Start a new mediation session and get a unique code</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-blue-600">2</span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">Invite Partner</h3>
                    <p class="text-slate-600 text-sm">Share the code with the other person</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">Share Perspectives</h3>
                    <p class="text-slate-600 text-sm">Take turns explaining your side</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-pink-600">4</span>
                    </div>
                    <h3 class="font-semibold text-slate-800 mb-2">AI Mediation</h3>
                    <p class="text-slate-600 text-sm">Dr. Harmony provides research-backed guidance</p>
                </div>
            </div>
        </div>

        <!-- Meet Dr. Harmony -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl shadow-xl p-8 text-white mb-16">
            <div class="flex items-start gap-6">
                <div class="w-20 h-20 bg-gradient-to-br from-teal-400 to-blue-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-2">Meet Dr. Harmony</h2>
                    <p class="text-slate-300 mb-4">Your AI Mediator</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-teal-500/20 text-teal-300 px-3 py-1 rounded-full text-sm">Gottman Method</span>
                        <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">NVC</span>
                        <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-sm">EFT</span>
                        <span class="bg-pink-500/20 text-pink-300 px-3 py-1 rounded-full text-sm">Harvard Negotiation</span>
                    </div>
                    <p class="text-slate-400 text-sm">
                        Dr. Harmony applies evidence-based therapeutic frameworks including the Gottman Method, Nonviolent Communication (NVC), and Emotionally Focused Therapy (EFT) to help you understand each other better.
                    </p>
                </div>
            </div>
        </div>

        <!-- Research Backed -->
        <div class="text-center">
            <h3 class="text-lg font-semibold text-slate-600 mb-4">Powered by Research</h3>
            <div class="flex flex-wrap justify-center gap-6 text-slate-500 text-sm">
                <span>"40+ years of Gottman research"</span>
                <span class="text-slate-300">|</span>
                <span>"94% prediction accuracy"</span>
                <span class="text-slate-300">|</span>
                <span>"5:1 positive interaction ratio"</span>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8 mt-16">
        <div class="max-w-6xl mx-auto px-4 text-center text-slate-500">
            <p>Built for Hackathon 2024 | Powered by Groq AI</p>
        </div>
    </footer>
</body>
</html>
