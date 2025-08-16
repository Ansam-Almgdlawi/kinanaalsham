<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterVolunteerRequest;
use App\Models\Project;
use App\Services\ProjectVolunteerService;
use Illuminate\Http\JsonResponse;

class ProjectVolunteerController extends Controller
{
    protected $projectVolunteerService;
    public function __construct(ProjectVolunteerService $projectVolunteerService)
    {
        $this->projectVolunteerService = $projectVolunteerService;
    }




    public function register(RegisterVolunteerRequest $request, $projectId): JsonResponse
    {
        try {
            $user = auth()->user();

            // التحقق من أن المستخدم متطوع (role_id = 5)
            if ($user->role_id !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'عفواً، التسجيل في الفعاليات متاح فقط للمتطوعين المسجلين'
                ], 403);
            }

            // التحقق من وجود الفعالية الربحية
            $project = project::find($projectId);
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفعالية الربحية غير موجودة'
                ], 404);
            }

            $data = [
                'project_id' => $project->id, // تغيير من event_id إلى revenue_event_id
                'user_id' => $user->id
            ];

            $result = $this->projectVolunteerService->registerVolunteer($data);

            return $result;



        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
