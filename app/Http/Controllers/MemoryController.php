<?php

namespace App\Http\Controllers;

use App\Models\UserMemory;
use App\Models\UserMemorySetting;
use App\Services\MemoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemoryController extends Controller
{
    /**
     * Show the memory dashboard
     */
    public function index(): View
    {
        $user = Auth::user();

        if (!$user->is_premium) {
            return view('memory.upgrade');
        }

        $memories = $user->memories()
            ->with('session')
            ->orderByDesc('created_at')
            ->get();

        $settings = UserMemorySetting::getOrCreate($user->id);

        // Group memories by conflict type for stats
        $byConflictType = $memories->groupBy('conflict_type');

        // Extract all unique themes
        $allThemes = $memories->flatMap(fn ($m) => $m->getThemes())->countBy()->sortDesc()->take(10);

        return view('memory.index', [
            'memories' => $memories,
            'settings' => $settings,
            'byConflictType' => $byConflictType,
            'topThemes' => $allThemes,
            'totalSessions' => $memories->count(),
        ]);
    }

    /**
     * Show a single memory
     */
    public function show(UserMemory $memory): View
    {
        $user = Auth::user();

        if ($memory->user_id !== $user->id) {
            abort(403);
        }

        return view('memory.show', [
            'memory' => $memory,
        ]);
    }

    /**
     * Delete a memory
     */
    public function destroy(UserMemory $memory, MemoryService $memoryService)
    {
        $user = Auth::user();

        if ($memory->user_id !== $user->id) {
            abort(403);
        }

        $memoryService->deleteMemory($memory);

        return redirect()->route('memory.index')
            ->with('success', 'Memory deleted successfully');
    }

    /**
     * Delete all memories
     */
    public function destroyAll(MemoryService $memoryService)
    {
        $user = Auth::user();
        $count = $memoryService->deleteAllMemories($user);

        return redirect()->route('memory.index')
            ->with('success', "Deleted {$count} memories");
    }

    /**
     * Update memory settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'memory_enabled' => 'required|boolean',
            'default_save_level' => 'required|in:all,summary,none',
        ]);

        $settings = UserMemorySetting::getOrCreate($user->id);
        $settings->update($validated);

        return redirect()->route('memory.index')
            ->with('success', 'Settings updated successfully');
    }
}
