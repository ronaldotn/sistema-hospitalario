<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\PractitionerController;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por Sanctum (temporalmente puedes comentar hasta integrar middleware)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// CRUD de pacientes
Route::prefix('patients')->group(function () {
    Route::get('/', [PatientController::class, 'index']);            // Listar pacientes
    Route::post('/', [PatientController::class, 'store']);           // Crear paciente
    Route::get('/{uuid}', [PatientController::class, 'show']);       // Mostrar paciente
    Route::put('/{uuid}', [PatientController::class, 'update']);     // Actualizar paciente
    Route::delete('/{uuid}', [PatientController::class, 'destroy']); // Eliminar paciente
});

// CRUD de practitioners (API RESTful)
Route::apiResource('practitioners', PractitionerController::class);
