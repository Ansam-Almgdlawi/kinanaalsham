<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseVoteRequest;
use App\Http\Resources\TrainingCourseResource;
use App\Models\TrainingCourse;
use App\Services\CourseVoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class CourseVoteController extends Controller
{protected $voteService;

    public function __construct(CourseVoteService $voteService)
    {
        $this->voteService = $voteService;
    }

    public function vote(CourseVoteRequest $request, $courseId)
    {

        $user = auth()->user();

        if (!$user || $user->role->id !== 5) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، هذا الاجراء خاص بالمتطوعين  فقط'
            ], 403);
        }

        $result = $this->voteService->vote($courseId);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        return new CourseVoteRequest($result);
    }

    public function index(): JsonResponse
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role->id, [5,6])) {
            abort(403, 'هذا الإجراء مسموح فقط للمستفيدين والمتطوعين!');
        }

        // تحديد نوع الجمهور المستهدف بناءً على دور المستخدم
        $targetAudience = ($user->role->id == 6) ? 'beneficiary' : 'volunteer';

        $courses = $this->voteService->getAllCoursesWithVotes()
            ->where('target_audience', $targetAudience); // تصفية حسب الجمهور المستهدف

        return response()->json([
            'success' => true,
            'data' => $courses,
            'message' => 'تم جلب الدورات الخاصة بـ ' . ($targetAudience == 'beneficiary' ? 'المستفيدين' : 'المتطوعين')
        ]);
    }

    public function show($id): JsonResponse
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role->id, [5,6])) {
            abort(403, 'هذا الإجراء مسموح فقط للمستفيدين والمتطوعين!');
        }

        $course = TrainingCourse::with('votes')->withCount('votes')->find($id);

        if (!$course) {
            return response()->json(['message' => 'الدورة غير موجودة'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TrainingCourseResource($course),
            'message' => 'تم جلب الدورة بنجاح'
        ]);
    }
}
