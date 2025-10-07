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
// Listar todos los pacientes
Route::get('/patients', [PatientController::class, 'index']);

// Mostrar un paciente específico
Route::get('/patients/{uuid}', [PatientController::class, 'show']);

// Crear un nuevo paciente
Route::post('/patients', [PatientController::class, 'store']);

// Actualizar un paciente existente
Route::put('/patients/{uuid}', [PatientController::class, 'update']);

// Eliminar un paciente
Route::delete('/patients/{uuid}', [PatientController::class, 'destroy']);
