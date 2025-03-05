<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TimesheetController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AttributeValueController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Project routes
    Route::apiResource('projects', ProjectController::class);

    Route::post('/projects/{id}/assign-user', [ProjectController::class, 'assignUser']);
    Route::post('/projects/{id}/remove-user', [ProjectController::class, 'removeUser']);


    // Timesheet routes
    Route::apiResource('timesheets', TimesheetController::class);
    Route::get('/timesheets/current-user', [TimesheetController::class, 'getTimesheetsForCurrentUser']);

    // Attribute routes
    Route::prefix('attributes')->group(function () {
        Route::get('/', [AttributeController::class, 'index']);
        Route::post('/', [AttributeController::class, 'store']);
        Route::get('/{attribute}', [AttributeController::class, 'show']);
        Route::put('/{attribute}', [AttributeController::class, 'update']);
        Route::delete('/{attribute}', [AttributeController::class, 'destroy']);
    });

    // AttributeValue routes
    Route::prefix('attribute-values')->group(function () {
        Route::post('/', [AttributeValueController::class, 'store']);
        Route::get('/{entityType}/{entityId}', [AttributeValueController::class, 'getEntityAttributes']);
    });

    // User Management Routes
    Route::apiResource('users', UserController::class)
        ->except(['store']); // Exclude store method as registration is handled separately
    Route::post('users/{id}/restore', [UserController::class, 'restore'])
        ->withTrashed();
});
