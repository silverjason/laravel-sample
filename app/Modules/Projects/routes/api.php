<?php

use App\Modules\Projects\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->prefix('api')
    ->group(function (): void {
        Route::middleware('auth')
            ->group(function (): void {
                Route::post('projects/{project}/complete', [ProjectController::class, 'complete'])
                    ->name('projects.complete');

                Route::apiResource('projects', ProjectController::class)
                    ->names('projects');
            });
    });
