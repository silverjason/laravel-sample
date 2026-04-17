<?php

use App\Modules\Tasks\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->prefix('api')
    ->group(function (): void {
        Route::middleware('auth')
            ->group(function (): void {
                Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])
                    ->name('tasks.complete');

                Route::apiResource('tasks', TaskController::class)
                    ->names('tasks');
            });
    });
