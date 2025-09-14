<?php


use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [RegisteredUserController::class, 'store'])->middleware('guest');
        Route::post('login',    [AuthenticatedSessionController::class, 'store'])->middleware('guest');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
        });
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('tasks/kanban', [TaskController::class, 'kanban'])->name('tasks.kanban');
        Route::apiResource('tasks', TaskController::class)->except(['create', 'edit']);

        Route::get('users', [UserController::class, 'index']);
        Route::patch('users/{user}/availability', [UserController::class, 'toggle']);
    });
});
