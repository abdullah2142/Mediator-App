<?php

use App\Http\Controllers\Api\SessionApiController;
use Illuminate\Support\Facades\Route;

// Session polling endpoint
Route::get('/session/{code}/status', [SessionApiController::class, 'status']);
