<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\QuizSessionController;
use Illuminate\Support\Facades\Route;

// --- Auth (public) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Routes protégées (Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::post('/quiz-sessions', [QuizSessionController::class, 'store']);
    Route::post('/quiz-sessions/{code}/join', [QuizSessionController::class, 'join']);
    Route::post('/quiz-sessions/{code}/answer', [QuizSessionController::class, 'answer']);
    Route::post('/quiz-sessions/{code}/finish', [QuizSessionController::class, 'finish']);
    Route::get('/quiz-sessions/{code}/leaderboard', [QuizSessionController::class, 'leaderboard']);

    Route::get('/leaderboard/global', [LeaderboardController::class, 'global']);
});
