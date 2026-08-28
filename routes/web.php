<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'QuizBattle API - voir /api pour les endpoints']);
});
