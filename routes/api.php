<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/


Route::post('/register', [AuthController::class, 'register']);
Route::middleware(['guest'])->post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Usuarios
    Route::get('users/all', [UserController::class, 'all']);
    Route::apiResource('users', UserController::class);

    // Proyectos
    Route::apiResource('projects', ProjectController::class);

    // Tareas
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::apiResource('tasks', TaskController::class);

    // Filtro de tareas (opcional, aunque index ya permite filtros por query)
    // /api/tasks?status=completed&project_id=1&assigned_to=2
});