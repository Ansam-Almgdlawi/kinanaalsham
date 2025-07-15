<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnounceCourseRequest;
use App\Http\Resources\TrainingCourseResource;
use App\Http\Resources\TrainingCourseWithVotesResource;
use App\Models\TrainingCourse;
use App\Services\TrainingCourseService;
use Illuminate\Http\JsonResponse;

class CourseAnnouncementController extends Controller
{
    protected $courseService;

    public function __construct(TrainingCourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function topVotedCourses()
    {
        // 1. جلب الدورات مع عدد التصويتات
        $courses = TrainingCourse::withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();

        // 2. تحديد أعلى عدد تصويتات
        $maxVotes = $courses->first()->votes_count ?? 0;

        // 3. تصفية الدورات الأعلى تصويتاً
        $topCourses = $courses->where('votes_count', $maxVotes);

        // 4. إرجاع النتيجة باستخدام Resource مع التصويتات
        return TrainingCourseResource::collection(
            $topCourses->load('votes') // تحميل علاقة التصويتات
        );
    }

    public function announce(AnnounceCourseRequest $request)
    {
        // 1. إلغاء إعلان جميع الدورات الأخرى (عدا الدورة الجديدة)
        TrainingCourse::where('is_announced', true)
            ->where('id', '!=', $request->course_id)
            ->update(['is_announced' => false]);

        // 2. حذف الدورات غير المعتمدة (عدا الدورة الجديدة)
        $deletedCount = TrainingCourse::where(function($query) use ($request) {
            $query->where('is_announced', false)
                ->orWhereNull('is_announced');
        })
            ->where('id', '!=', $request->course_id)
            ->where('created_at', '<', now()->subWeek())
            ->delete();

        // 3. إعلان الدورة الجديدة
        $course = TrainingCourse::findOrFail($request->course_id);
        $course->update([
            'is_announced' => true,
            'announcement_text' => $request->announcement_text,
             'max_volunteers' => $request->max_volunteers, // العدد الأقصى للمتطوعين
             'current_volunteers' => 0 // يبدأ من الصفر
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الإعلان عن الدورة (عدد المقاعد: '.$request->max_volunteers.') وحذف ' . $deletedCount . ' دورات غير معتمدة',
            'data' => new TrainingCourseResource($course)
        ]);
    }
}
