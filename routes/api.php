<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PatientController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    //ralimit, franz
});
// =====================
// Pacientes existentes
// =====================
Route::get('/patients', [PatientController::class, 'index']);
Route::get('/metrics', [PatientController::class, 'metrics']);
Route::get('/patients/{uuid}', [PatientController::class, 'show']);
Route::post('/patients', [PatientController::class, 'store']);
Route::put('/patients/{uuid}', [PatientController::class, 'update']);
Route::delete('/patients/{uuid}', [PatientController::class, 'destroy']);

// =====================
// Duplicados
// =====================

// 🔹 Detectar duplicados potenciales
Route::get('/patients/duplicates', [PatientController::class, 'duplicates']);

// 🔹 Fusionar duplicados (solo admin)
Route::post('/patients/merge', [PatientController::class, 'mergeDuplicates']);
