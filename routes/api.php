<?php




use App\Http\Controllers\Api\Admin\CourseAnnouncementController;
use App\Http\Controllers\Api\CourseVoteController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\VolunteerBadgeController;
use App\Http\Controllers\Api\VolunteerRegistrationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventPostController;
use App\Http\Controllers\EventRatingController;
use App\Http\Controllers\EventVolunteerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpportunityApplicationController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\AssistanceRequestController;
use App\Http\Controllers\EmergencyRequestController;
use App\Http\Controllers\HonorBoardController;
use App\Http\Controllers\InquiryController;

use App\Http\Controllers\ProjectBeneficiaryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPostController;
use App\Http\Controllers\ProjectRatingController;
use App\Http\Controllers\ProjectVolunteerController;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\VolunteerProfileController;
use App\Http\Controllers\WarehouseController;
use App\Models\BeneficiaryDocument;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;

use App\Http\Controllers\Api\SuccessStoryController;
use App\Http\Controllers\Api\VolunteerApplicationController;
use App\Http\Controllers\Api\TrainingCourseController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Admin\AdminLoginController;
use App\Http\Controllers\API\BeneficiaryController;
use App\Http\Middleware\CheckAdminProjectManager;
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
Route::get('/honor-board', [HonorBoardController::class, 'show']);
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
    Route::post('/inquiries', [InquiryController::class, 'store']);
    Route::get('/all-inquiries', [InquiryController::class, 'indexBeneficiary']);
    Route::get('/emergency-requests/my-area', [EmergencyRequestController::class, 'showMyAreaRequests']);
    Route::post('/emergency-requests/{request}/accept', [EmergencyRequestController::class, 'acceptRequest']);
    Route::put('/volunteer/profile', [VolunteerProfileController::class, 'update']);
    Route::get('/profile', [VolunteerProfileController::class, 'show']);


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
        // للأدمن: عرض جميع الاستفسارات
        Route::get('/inquiries', [InquiryController::class, 'indexAdmin']);

        //  للأدمن: الرد على استفسار معين
        Route::put('/inquiries/{inquiry}/reply', [InquiryController::class, 'reply']);
        //  للأدمن: عرض جميع الطلبات
        Route::get('/assistance-requests', [AssistanceRequestController::class, 'indexAdmin']);

        // للأدمن: تحديث حالة طلب (قبول، رفض، ...)
        Route::put('/assistance-requests/{assistanceRequest}', [AssistanceRequestController::class, 'updateStatus']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::get('/all-volunteers', [UserController::class, 'index']);
        // المستفيدين
        Route::post('beneficiaries/{user}/status', [BeneficiaryController::class, 'updateStatus']);
        Route::get('beneficiaries/pending', [BeneficiaryController::class, 'pending']);
        Route::get('beneficiaries/{user}', [BeneficiaryController::class, 'show']);
        Route::post('beneficiaries/success-stories', [SuccessStoryController::class, 'store']);
        Route::get('/my-notifications', [NotificationController::class, 'myNotifications']);
    });
});

/*
|--------------------------------------------------------------------------
| Training Courses
|--------------------------------------------------------------------------
*/


Route::post('training-courses',[TrainingCourseController::class,'store'])->middleware('auth:api','admin.projectmanager');
Route::group(['middleware' => ['role:admin'|'Project Manager']], function () {
    //Route::post('/training-courses', [TrainingCourseController::class, 'store'])->middleware('auth:sanctum');
});

Route::get('/courses', [CourseVoteController::class, 'index'])->middleware(['auth:api', 'beneficiary.volunteer']);
Route::post('{courseId}/vote', [CourseVoteController::class, 'vote'])->middleware(['auth:sanctum']);

Route::get('/courses/{id}', [CourseVoteController::class, 'show'])->middleware(['auth:api', 'beneficiary.volunteer']);
Route::get('/top-courses', [CourseAnnouncementController::class, 'topVotedCourses'])->middleware('auth:api','admin.projectmanager');
Route::post('/announce', [CourseAnnouncementController::class, 'announce'])->middleware('auth:api','admin.projectmanager');
Route::get('/volunteer/news', [NewsController::class, 'getAnnouncedCourse'])->middleware(['auth:api', 'beneficiary.volunteer']);
Route::post('/courses/{id}/register', [VolunteerRegistrationController::class, 'register'])->middleware(['auth:api', 'beneficiary.volunteer']);
Route::get('/courses/{courseId}/registrations', [VolunteerRegistrationController::class, 'showRegistrations'])
    ->middleware('auth:api','admin.projectmanager');


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

});
/*
|--------------------------------------------------------------------------
| Event APIs
|--------------------------------------------------------------------------
*/

    Route::post('/events', [EventController::class, 'store'])
        ->middleware('auth:api','admin.projectmanager');
    Route::post('/events/by-month', [EventController::class, 'getEventsByMonth']);
       // ->middleware('auth:api','admin.projectmanager');
    Route::post('/events/by-date', [EventController::class, 'getEventsByDate']);
       // ->middleware('auth:api','admin.projectmanager');
    Route::post('/events/{event}/register', [EventVolunteerController::class, 'register'])
        ->middleware(['auth:sanctum']);
    Route::get('volunteer/events', [EventVolunteerController::class, 'getMyEvents'])
    ->middleware('auth:sanctum');
    Route::post('event/posts', [EventPostController::class, 'store'])
        ->middleware('auth:api','admin.projectmanager');

    Route::get('published-events', [EventController::class, 'getPublishedEvents']);
      // ->middleware(['auth:api', 'beneficiary.volunteer']);

    Route::post('events/{event}/rate', [EventRatingController::class, 'store'])
        ->middleware(['auth:api', 'beneficiary.volunteer']);

    Route::get('events/{event}/comments', [EventRatingController::class, 'getComments']);
  //  ->middleware('auth:api','admin.projectmanager');

    Route::get('events/{event}/average-rating', [EventRatingController::class, 'getAverageRating']);
   // ->middleware('auth:api','admin.projectmanager');

///////////جديد
Route::post('/login-project-manager', [AdminLoginController::class, 'loginProjectManager']);
Route::post('/logout', [AdminLoginController::class, 'logout'])
    ->middleware('auth:sanctum');
Route::post('/in-kind-donations', [DonationController::class, 'store']);
Route::post('/warehouses', [WarehouseController::class, 'store']);
Route::post('/warehouses/{warehouse}/add-donation/{donation}', [WarehouseController::class, 'addDonation']);
Route::get('/warehouses/{warehouse}/inventory', [WarehouseController::class, 'inventory']);
Route::get('/in-kind-donations/pending', [DonationController::class, 'pendingDonations']);
Route::post('/distributions', [DistributionController::class, 'store']); // إنشاء توزيع
Route::post('/distributions/{id}/mark-received', [DistributionController::class, 'markReceived']); // تسليم مستلم
Route::get('/distributions/{id}', [DistributionController::class, 'show']); // عرض توزيع
Route::get('/monthly-report/{month}/{year}', [ReportController::class, 'monthlyReport']);
Route::get('/statistics', [ReportController::class, 'getDashboardStats']);
Route::get('/volunteer-of-week', [VolunteerBadgeController::class, 'volunteerOfTheWeek']);
Route::get('/volunteer-of-month', [VolunteerBadgeController::class, 'volunteerOfTheMonth']);
    /*
    ----------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/success-stories', [SuccessStoryController::class, 'store']);
        Route::get('/profile', [BeneficiaryController::class, 'profile']);
        Route::post('/update-profile', [BeneficiaryController::class, 'updateProfile']);
        Route::delete('/documents/{document}', [BeneficiaryController::class, 'destroy']);

        Route::post('/inquiries', [InquiryController::class, 'store']);

        // 2. للمستفيد: عرض جميع استفساراته مع الردود
        Route::get('/inquiries', [InquiryController::class, 'indexBeneficiary']);
        // 3. للمستفيد: عرض استفسار معين له
        Route::get('/inquiries/{inquiry}', [InquiryController::class, 'showBeneficiary']);
        // --- مسارات طلبات المساعدة ---

        // 1. للمستفيد: تقديم طلب مساعدة جديد
        Route::post('/assistance-requests', [AssistanceRequestController::class, 'store']);
        // 2. للمستفيد: عرض جميع طلباته
        Route::get('/assistance-requests', [AssistanceRequestController::class, 'index']);
        Route::post('/emergency-requests', [EmergencyRequestController::class, 'store']);
        Route::get('/emergency/my-requests', [EmergencyRequestController::class, 'showMyEmergencyRequests']);
        Route::get('/my-certificates', [CertificateController::class, 'getUserCertificates']);
    });

/*
|--------------------------------------------------------------------------
| Projects APIs
|--------------------------------------------------------------------------
*/
Route::post('projects', [ProjectController::class, 'store'])
    ->middleware('auth:api','admin.projectmanager');
Route::get('show/projects', [ProjectController::class,'index']);

Route::middleware('auth:api')->group(function() {
    // لعرض متطلبات فعالية معينة (للجميع)
    Route::get('projects/{project}/requirements', [ProjectController::class, 'showRequirements']);

    // لتحديث المتطلبات (للأدمن فقط)
    Route::put('events/{event}/requirements', [ProjectController::class, 'updateRequirements']);});
Route::post('register/{project}', [ProjectBeneficiaryController::class, 'register'])->middleware('auth:sanctum');
Route::post('/projects/{project}/register', [ProjectVolunteerController::class, 'register'])
    ->middleware(['auth:sanctum']);
// للمتطوعين فقط
Route::get('/volunteer/projects', [ProjectController::class, 'getMyProjects'])->middleware(['auth:sanctum']);
Route::post('project/posts', [ProjectPostController ::class, 'store'])
    ->middleware('auth:api','admin.projectmanager');
Route::get('published-projects', [ProjectPostController::class, 'getPublishedProjects'])
    ->middleware(['auth:api', 'beneficiary.volunteer']);
Route::post('projects/{project}/ratings', [ProjectRatingController::class, 'store'])
    ->middleware(['auth:api', 'beneficiary.volunteer']);

Route::get('projects/{project}/ratings/comments', [ProjectRatingController::class, 'getComments'])
    ->middleware('auth:api','admin.projectmanager');

Route::get('projects/{project}/ratings/average', [ProjectRatingController::class, 'getAverageRating'])
    ->middleware('auth:api','admin.projectmanager');
Route::post('/donate', [StripePaymentController::class, 'donate']);


Route::post('/stripe/webhook', [StripePaymentController::class, 'handleWebhook']);
Route::get('/fund', function() {
    $fund = Fund::first();
    return response()->json([
        'name' => $fund?->name ?? 'No Account',
        'balance' => $fund?->balance ?? 0,
    ]);
});
Route::get('/events/{id}', [EventController::class, 'show']);
