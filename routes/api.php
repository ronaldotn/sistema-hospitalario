<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\EncounterController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\OrganizationController;
use App\Http\Controllers\API\PractitionerController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    //ralimit, franz
});
Route::get('/patients', [PatientController::class, 'index']);
Route::post('/patients', [PatientController::class, 'store']);
Route::get('/patients/{uuid}', [PatientController::class, 'show']);
Route::get('/practitioners', [PractitionerController::class, 'index']);
Route::post('/practitioners', [PractitionerController::class, 'store']);
Route::apiResource('organizations', OrganizationController::class)
    ->parameters(['organizations' => 'uuid']);
Route::apiResource('encounters', EncounterController::class);
