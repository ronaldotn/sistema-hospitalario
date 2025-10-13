<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\PractitionerController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    //ralimit, franz
});
// Listar todos los pacientes

Route::get('/practitioners', [PractitionerController::class, 'index']);
Route::post('/practitioners', [PractitionerController::class, 'store']);
// La URL final será: /api/practitioners/lookup
Route::get('practitioners/lookup', [PractitionerController::class, 'lookup'])
    ->name('practitioner.lookup'); // Alias claro, profesional y en CamelCase.
Route::post('practitioners/check', [PractitionerController::class, 'checkUnique'])
    ->name('practitioner.check');
Route::put('/practitioners/{id}', [PractitionerController::class, 'update']);
Route::patch('/practitioners/{id}', [PractitionerController::class, 'updatePartial']);
