<?php

use App\Http\Controllers\OpportunityApplicationController;
use App\Http\Controllers\OpportunityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;

// ==== Volunteer Application APIs ====
use App\Http\Controllers\Api\VolunteerApplicationController;
use App\Http\Controllers\Api\TrainingCourseController; // ← من فرع master

// ==== Authentication APIs ====
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;

// ==== Admin APIs ====
use App\Http\Controllers\Api\Admin\AdminLoginController;

/*
|--------------------------------------------------------------------------
| Volunteer Public APIs
|--------------------------------------------------------------------------
| استمارة التقديم على التطوع واستعراض الصور
*/
Route::prefix('volunteer')->group(function () {
    Route::post('apply', [VolunteerApplicationController::class, 'store']);
    Route::get('applications', [VolunteerApplicationController::class, 'index']);
    Route::get('applications/{id}', [VolunteerApplicationController::class, 'show']);
    Route::put('applications/{id}/status', [VolunteerApplicationController::class, 'updateStatus']);
});
Route::get('volunteers/{id}/profile-picture', [VolunteerApplicationController::class, 'showProfilePicture']);
Route::get('/opportunities/jobs', [OpportunityController::class, 'indexJobs']);
Route::get('/opportunities/volunteering', [OpportunityController::class, 'indexVolunteering']);

/*
|--------------------------------------------------------------------------
| Volunteer Authentication APIs
|--------------------------------------------------------------------------
| تسجيل وإنشاء حساب متطوع وتسجيل الدخول والخروج
*/
Route::post('/register', RegisterController::class)->name('api.register');
Route::post('/login', LoginController::class)->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', LogoutController::class)->name('api.logout');
    Route::post('/applications', [OpportunityApplicationController::class, 'apply']);
    Route::get('/my-applications', [OpportunityApplicationController::class, 'indexUserApplications']);

    Route::get('/user', function (Request $request) {
        return new UserResource(
            $request->user()->load('volunteerDetails', 'role')
        );
    });
});

/*
|--------------------------------------------------------------------------
| Admin Authentication APIs
|--------------------------------------------------------------------------
| تسجيل دخول وخروج الأدمن فقط
*/
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminLoginController::class, 'login'])->name('admin.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('opportunities', [OpportunityController::class, 'store']);
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
        Route::patch('/opportunities/{id}/status', [OpportunityController::class, 'updateStatus']);
        Route::get('applications/pending', [OpportunityApplicationController::class, 'indexPendingApplications']);
        Route::patch('applications/{id}/status', [OpportunityApplicationController::class, 'updateStatus']);
    });
});

/*
|--------------------------------------------------------------------------
| Training Courses APIs
|--------------------------------------------------------------------------
*/
Route::post('/training-courses', [TrainingCourseController::class, 'store']);
