<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use App\Models\MediationSession;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    $activeSession = null;
    $participants = session('participants', []);
    if (is_array($participants) && count($participants) > 0) {
        $codes = array_keys($participants);
        $activeSession = MediationSession::query()
            ->whereIn('code', $codes)
            ->where('status', '!=', MediationSession::STATUS_COMPLETED)
            ->first();
    }
    return view('welcome', ['activeSession' => $activeSession]);
})->name('home');

// Session routes (no auth required - supports guest mode)
Route::get('/session/create', [SessionController::class, 'create'])->name('session.create');
Route::post('/session/create', [SessionController::class, 'store'])->name('session.store');
Route::get('/session/join', [SessionController::class, 'join'])->name('session.join');
Route::post('/session/join', [SessionController::class, 'doJoin'])->name('session.doJoin');
Route::get('/session/{code}', [SessionController::class, 'room'])->name('session.room');
Route::post('/session/{code}/message', [SessionController::class, 'sendMessage'])->name('session.message');
Route::post('/session/{code}/end', [SessionController::class, 'end'])->name('session.end');
Route::view('/pricing', 'pricing')->name('pricing');
Route::get('/rooms', [SessionController::class, 'rooms'])->name('rooms');

// Dashboard (for logged in users)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
