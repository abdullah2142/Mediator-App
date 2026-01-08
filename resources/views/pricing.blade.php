<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - MediAItor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-teal-50 min-h-screen text-slate-800">
    <!-- Navigation (PUBLIC) -->
    <nav class="bg-white/80 backdrop-blur-sm border-b border-slate-200 sticky top-0 z-50">
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

            <div class="flex items-center gap-4">
                <a href="{{ route('pricing') }}" class="text-slate-600 hover:text-slate-800">Pricing</a>

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

    <main class="max-w-6xl mx-auto px-4 py-12 md:py-14">
        <!-- Header -->
        <section class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-3">
                Pricing that scales from 1:1 to groups
            </h1>
            <p class="text-slate-600 text-lg max-w-2xl mx-auto">
                Start free for one-on-one mediation. Upgrade when you need group rooms, moderation tools, and structured action plans.
            </p>
        </section>

        <!-- Billing Toggle -->
        <section class="flex justify-center mb-10">
            <div class="bg-white/80 border border-slate-200 rounded-2xl p-2 inline-flex items-center gap-2 shadow-sm">
                <button id="billMonthly"
                        type="button"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-900 text-white">
                    Monthly
                </button>
                <button id="billYearly"
                        type="button"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-white">
                    Yearly <span class="text-teal-700">(save 20%)</span>
                </button>
            </div>
        </section>

        <!-- Cards -->
        <section class="grid lg:grid-cols-4 gap-5 items-stretch">
            <!-- Free -->
            <div class="bg-white/80 border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold">Free</h2>
                        <p class="text-slate-600 text-sm mt-1">Best for 1:1, quick fixes.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">
                            <span class="price" data-month="৳0" data-year="৳0">৳0</span>
                        </p>
                        <p class="text-xs text-slate-500">forever</p>
                    </div>
                </div>

                <ul class="mt-5 space-y-3 text-sm text-slate-700 flex-1">
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-500"></span>2-person rooms</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-500"></span>Turn-based messaging</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-500"></span>Basic AI reflection after each round</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-500"></span>1 active room at a time</li>
                </ul>

                <a href="{{ url('/') }}"
                   class="mt-6 inline-flex w-full justify-center bg-white text-slate-700 px-4 py-3 rounded-xl font-semibold border-2 border-slate-200 hover:border-teal-300 hover:text-teal-700 transition-all">
                    Start free
                </a>
            </div>

            <!-- Starter (Couples) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold">Starter</h2>
                        <p class="text-slate-600 text-sm mt-1">For couples / close friends.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">
                            <span class="price" data-month="৳99" data-year="৳79">৳99</span>
                        </p>
                        <p class="text-xs text-slate-500"><span class="per">per month</span></p>
                    </div>
                </div>

                <ul class="mt-5 space-y-3 text-sm text-slate-700 flex-1">
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-blue-500"></span>Everything in Free</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-blue-500"></span>Smarter “common ground” summary</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-blue-500"></span>Export summary (copy / share)</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-blue-500"></span>Up to 3 active rooms</li>
                </ul>

                <a href="{{ route('register') }}"
                   class="mt-6 inline-flex w-full justify-center bg-white text-slate-700 px-4 py-3 rounded-xl font-semibold border-2 border-slate-200 hover:border-teal-300 hover:text-teal-700 transition-all">
                    Get Starter
                </a>
            </div>

            <!-- Premium (Group) - Highlight -->
            <div class="bg-white border-2 border-teal-200 rounded-2xl p-6 shadow-xl flex flex-col relative overflow-hidden">
                <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full bg-gradient-to-br from-teal-200 to-blue-200 blur-2xl opacity-60" aria-hidden="true"></div>

                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-teal-50 border border-teal-200 rounded-full px-3 py-1 text-xs text-teal-700 mb-3">
                            Most popular
                        </div>
                        <h2 class="text-lg font-bold">Premium (Group)</h2>
                        <p class="text-slate-600 text-sm mt-1">Roommates, teams, clubs.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">
                            <span class="price" data-month="৳199" data-year="৳159">৳199</span>
                        </p>
                        <p class="text-xs text-slate-500"><span class="per">per month</span></p>
                    </div>
                </div>

                <ul class="relative mt-5 space-y-3 text-sm text-slate-700 flex-1">
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-600"></span>Group rooms (up to 6 people)</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-600"></span>Round-robin turns + optional timer</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-600"></span>Action Plan output (agreements + next steps)</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-600"></span>Moderator controls (lock joins, pause, reorder)</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-teal-600"></span>Up to 10 active rooms</li>
                </ul>

                <a href="{{ route('register') }}"
                   class="relative mt-6 inline-flex w-full justify-center bg-gradient-to-r from-teal-600 to-blue-600 text-white px-4 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-teal-500/20 transition-all">
                    Buy Premium
                </a>

                <p class="relative text-xs text-slate-500 text-center mt-3">
                    Hackathon-friendly: billing can be “coming soon” — show value first.
                </p>
            </div>

            <!-- Pro (Organization) -->
            <div class="bg-white/80 border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold">Pro</h2>
                        <p class="text-slate-600 text-sm mt-1">For organizations & communities.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">
                            <span class="price" data-month="৳499" data-year="৳399">৳499</span>
                        </p>
                        <p class="text-xs text-slate-500"><span class="per">per month</span></p>
                    </div>
                </div>

                <ul class="mt-5 space-y-3 text-sm text-slate-700 flex-1">
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-purple-500"></span>Everything in Premium</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-purple-500"></span>Bigger rooms (up to 12 people)</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-purple-500"></span>Roles: host, co-host, participant</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-purple-500"></span>Session templates (house rules, team norms)</li>
                    <li class="flex gap-2"><span class="mt-1 w-2 h-2 rounded-full bg-purple-500"></span>Unlimited active rooms</li>
                </ul>

                <a href="{{ route('register') }}"
                   class="mt-6 inline-flex w-full justify-center bg-white text-slate-700 px-4 py-3 rounded-xl font-semibold border-2 border-slate-200 hover:border-teal-300 hover:text-teal-700 transition-all">
                    Get Pro
                </a>

                <a href="mailto:hello@mediaitor.local"
                   class="mt-3 inline-flex w-full justify-center text-slate-600 px-4 py-3 rounded-xl font-semibold hover:text-slate-800 transition-all">
                    Contact sales
                </a>
            </div>
        </section>

        <!-- Comparison -->
        <section class="mt-10 bg-white/80 border border-slate-200 rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Feature comparison</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-600">
                            <th class="py-2">Feature</th>
                            <th class="py-2">Free</th>
                            <th class="py-2">Starter</th>
                            <th class="py-2">Premium</th>
                            <th class="py-2">Pro</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        <tr class="border-t border-slate-200">
                            <td class="py-3">Max participants</td>
                            <td class="py-3">2</td>
                            <td class="py-3">2</td>
                            <td class="py-3">6</td>
                            <td class="py-3">12</td>
                        </tr>
                        <tr class="border-t border-slate-200">
                            <td class="py-3">Turn-taking</td>
                            <td class="py-3">✅</td>
                            <td class="py-3">✅</td>
                            <td class="py-3">✅ Round-robin</td>
                            <td class="py-3">✅ Round-robin</td>
                        </tr>
                        <tr class="border-t border-slate-200">
                            <td class="py-3">Turn timer</td>
                            <td class="py-3">—</td>
                            <td class="py-3">—</td>
                            <td class="py-3">✅</td>
                            <td class="py-3">✅</td>
                        </tr>
                        <tr class="border-t border-slate-200">
                            <td class="py-3">AI summaries</td>
                            <td class="py-3">Basic</td>
                            <td class="py-3">Better</td>
                            <td class="py-3">Action plan</td>
                            <td class="py-3">Action plan + templates</td>
                        </tr>
                        <tr class="border-t border-slate-200">
                            <td class="py-3">Moderator controls</td>
                            <td class="py-3">—</td>
                            <td class="py-3">—</td>
                            <td class="py-3">✅</td>
                            <td class="py-3">✅ + roles</td>
                        </tr>
                        <tr class="border-t border-slate-200">
                            <td class="py-3">Active rooms</td>
                            <td class="py-3">1</td>
                            <td class="py-3">3</td>
                            <td class="py-3">10</td>
                            <td class="py-3">Unlimited</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- FAQ -->
        <section class="mt-10 grid md:grid-cols-2 gap-5">
            <div class="bg-white/80 border border-slate-200 rounded-2xl p-6">
                <h4 class="font-semibold text-slate-800 mb-2">Can people view pricing without login?</h4>
                <p class="text-sm text-slate-600">
                    Yes — pricing is public. Only purchase/upgrade flows need an account.
                </p>
            </div>
            <div class="bg-white/80 border border-slate-200 rounded-2xl p-6">
                <h4 class="font-semibold text-slate-800 mb-2">What does “Action Plan” mean?</h4>
                <p class="text-sm text-slate-600">
                    After a round, Dr. Harmony outputs: common ground, each person’s needs, and 2–3 specific next steps to try.
                </p>
            </div>
        </section>

        <section class="mt-10 text-center text-sm text-slate-500">
            <p>MediAItor is a communication tool — not therapy, legal advice, or emergency support.</p>
        </section>
    </main>

    <footer class="bg-white border-t border-slate-200 py-8 mt-10">
        <div class="max-w-6xl mx-auto px-4 text-center text-slate-500">
            <p>Built for Hackathon 2026 | Powered by AI and Coffee</p>
        </div>
    </footer>

    <!-- Tiny JS for monthly/yearly toggle -->
    <script>
        (function () {
            const monthlyBtn = document.getElementById('billMonthly');
            const yearlyBtn = document.getElementById('billYearly');
            const prices = document.querySelectorAll('.price');
            const pers = document.querySelectorAll('.per');

            function setMode(mode) {
                const isYear = mode === 'year';

                // button styles
                if (isYear) {
                    yearlyBtn.classList.add('bg-slate-900', 'text-white');
                    yearlyBtn.classList.remove('text-slate-700');
                    monthlyBtn.classList.remove('bg-slate-900', 'text-white');
                    monthlyBtn.classList.add('text-slate-700');
                } else {
                    monthlyBtn.classList.add('bg-slate-900', 'text-white');
                    monthlyBtn.classList.remove('text-slate-700');
                    yearlyBtn.classList.remove('bg-slate-900', 'text-white');
                    yearlyBtn.classList.add('text-slate-700');
                }

                // update prices
                prices.forEach(el => {
                    el.textContent = isYear ? el.dataset.year : el.dataset.month;
                });

                // update "per" label
                pers.forEach(el => {
                    el.textContent = isYear ? 'per month (billed yearly)' : 'per month';
                });
            }

            monthlyBtn?.addEventListener('click', () => setMode('month'));
            yearlyBtn?.addEventListener('click', () => setMode('year'));

            // default
            setMode('month');
        })();
    </script>
</body>
</html>
