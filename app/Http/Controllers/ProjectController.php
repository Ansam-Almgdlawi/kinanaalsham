<?php

namespace App\Http\Controllers;

use App\Services\ProjectVolunteerService;
use Illuminate\Http\Request;
use App\Http\Requests\CreateProjectRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{

    protected $projectVolunteerService;

    public function __construct(ProjectVolunteerService $projectVolunteerService)
    {
        $this->projectVolunteerService = $projectVolunteerService;
    }
    public function store(CreateProjectRequest $request)
    {


        $project = Project::create([
            'project_number' => $request->project_number,
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'objective' => $request->objective,
            'status' => $request->status ?? 'planning',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'requirements' => $request->requirements,
            'created_by_user_id' => auth()->id(),
            'max_beneficiaries' => $request->max_beneficiaries
        ]);

        return response()->json([
            'message' => 'تم إنشاء المشروع الربحي بنجاح',
            'data' => $project
        ], 201);
    }


    // عرض جميع المشاريع
    public function index()
    {
        $projects = Project::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }


    // عرض متطلبات فعالية محددة
    public function showRequirements($eventId)
    {
        $event = Project::select('id', 'name', 'requirements')
            ->findOrFail($eventId);

        return response()->json([
            'success' => true,
            'data' => [
                'event_name'   => $event->name,
                'requirements' => collect(explode(',', $event->requirements))
                    ->map(fn($item) => trim($item)) // يشيل المسافات
                    ->values() // يرجع list مرتبة بالأرقام 0,1,2
            ]
        ]);
    }

    // تحديث متطلبات الفعالية (للأدمن فقط)
    public function updateRequirements(Request $request, $eventId)
    {
        $event = Project::findOrFail($eventId);

        // التحقق من الصلاحيات يدوياً (طبقة حماية إضافية)
        $user = auth()->user();

        if (!$user || !in_array($user->role->id, [1, 2])) {
            abort(403, 'هذا الإجراء مسموح فقط للادمن والبروجيكت مانجر!');
        }

        $validated = $request->validate([
            'requirements' => 'required|string'
        ]);

        $event->update(['requirements' => $validated['requirements']]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المتطلبات بنجاح',
            'data' => $event->requirements
        ]);
    }






    /**
     * عرض جميع الفعاليات الربحية التي شارك بها المتطوع
     */
    public function getMyProjects()
    {
        try {
            $user = auth()->user();

            // التحقق من أن المستخدم متطوع (role_id = 5)
            if ($user->role_id !== 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'عفواً، هذا الإجراء متاح فقط للمتطوعين المسجلين'
                ], 403);
            }
            return $this->projectVolunteerService->getVolunteerProjects($user->id);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

