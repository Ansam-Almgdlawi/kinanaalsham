<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;

// ==== Controllers ====
use App\Http\Controllers\Api\SuccessStoryController;
use App\Http\Controllers\Api\VolunteerApplicationController;
use App\Http\Controllers\Api\TrainingCourseController;
use App\Http\Controllers\Api\CourseVoteController;
use App\Http\Controllers\Api\Admin\CourseAnnouncementController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\VolunteerRegistrationController;
use App\Http\Controllers\OpportunityApplicationController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Admin\AdminLoginController;
use App\Http\Controllers\API\BeneficiaryController;

/*
|--------------------------------------------------------------------------
| Volunteer APIs
|--------------------------------------------------------------------------
*/
Route::prefix('volunteer')->group(function () {
    Route::post('apply', [VolunteerApplicationController::class, 'store']);
    Route::get('applications', [VolunteerApplicationController::class, 'index']);
    Route::get('applications/{id}', [VolunteerApplicationController::class, 'show']);
    Route::put('applications/{id}/status', [VolunteerApplicationController::class, 'updateStatus']);
});
Route::get('volunteers/{id}/profile-picture', [VolunteerApplicationController::class, 'showProfilePicture']);

/*
|--------------------------------------------------------------------------
| Opportunity APIs
|--------------------------------------------------------------------------
*/
Route::get('/opportunities/jobs', [OpportunityController::class, 'indexJobs']);
Route::get('/opportunities/volunteering', [OpportunityController::class, 'indexVolunteering']);

/*
|--------------------------------------------------------------------------
| Volunteer Authentication
|--------------------------------------------------------------------------
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

    // التصويت على الدورات
    Route::get('/courses', [CourseVoteController::class, 'index']);
    Route::post('{courseId}/vote', [CourseVoteController::class, 'vote']);
});

/*
|--------------------------------------------------------------------------
| Admin Authentication and APIs
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminLoginController::class, 'login'])->name('admin.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
        Route::post('opportunities', [OpportunityController::class, 'store']);
        Route::patch('/opportunities/{id}/status', [OpportunityController::class, 'updateStatus']);
        Route::get('applications/pending', [OpportunityApplicationController::class, 'indexPendingApplications']);
        Route::patch('applications/{id}/status', [OpportunityApplicationController::class, 'updateStatus']);
        Route::get('success-stories/pending', [SuccessStoryController::class, 'pending']);
        Route::patch('success-stories/{id}/status', [SuccessStoryController::class, 'approve']);

        // المستفيدين
        Route::post('beneficiaries/{user}/status', [BeneficiaryController::class, 'updateStatus']);
        Route::get('beneficiaries/pending', [BeneficiaryController::class, 'pending']);
        Route::get('beneficiaries/{user}', [BeneficiaryController::class, 'show']);
        Route::post('beneficiaries/success-stories', [SuccessStoryController::class, 'store']);
    });
});

/*
|--------------------------------------------------------------------------
| Training Courses
|--------------------------------------------------------------------------
*/
Route::post('/training-courses', [TrainingCourseController::class, 'store']);

Route::get('/courses', [CourseVoteController::class, 'index'])->middleware('auth:sanctum');
Route::post('{courseId}/vote', [CourseVoteController::class, 'vote'])->middleware('auth:sanctum');
Route::get('/courses/{id}', [CourseVoteController::class, 'show'])->middleware('auth:sanctum');
Route::get('/top-courses', [CourseAnnouncementController::class, 'topVotedCourses'])->middleware('auth:sanctum');
Route::post('/announce', [CourseAnnouncementController::class, 'announce'])->middleware('auth:sanctum');
Route::get('/volunteer/news', [NewsController::class, 'getAnnouncedCourse'])->middleware('auth:sanctum');
Route::post('/courses/{id}/register', [VolunteerRegistrationController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Success Stories (Public APIs)
|--------------------------------------------------------------------------
*/
Route::get('/success-stories', [SuccessStoryController::class, 'approvedStories']);
Route::get('/success-stories/{id}', [SuccessStoryController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Beneficiary APIs
|--------------------------------------------------------------------------
*/
Route::prefix('beneficiaries')->group(function () {
    Route::post('/register', [BeneficiaryController::class, 'store']);
    Route::post('/login', [LoginController::class, 'beneficiaryLogin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/success-stories', [SuccessStoryController::class, 'store']);
    });
});
