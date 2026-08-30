<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArtisanController;
use App\Http\Controllers\Api\DemandeController;
use App\Http\Controllers\Api\AvisController;

// Routes publiques (pas besoin d'être connecté)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées (nécessitent un token valide)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/artisans/search', [ArtisanController::class, 'search']);
    Route::get('/artisans/{id}', [ArtisanController::class, 'show']);
    Route::post('/demandes', [DemandeController::class, 'store']);
    Route::get('/demandes', [DemandeController::class, 'index']);
    Route::patch('/demandes/{id}/confirmer', [DemandeController::class, 'confirmer']);
    Route::post('/avis', [AvisController::class, 'store']);
});



