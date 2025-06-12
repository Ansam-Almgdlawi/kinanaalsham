<?php

use App\Http\Controllers\Api\VolunteerApplicationController;
use Illuminate\Support\Facades\Route;


Route::prefix('volunteer')->group(function () {
    Route::post('apply', [VolunteerApplicationController::class, 'store']);
    Route::get('applications', [VolunteerApplicationController::class, 'index']);
    Route::get('applications/{id}', [VolunteerApplicationController::class, 'show']);
    Route::put('applications/{id}/status', [VolunteerApplicationController::class, 'updateStatus']);
});
