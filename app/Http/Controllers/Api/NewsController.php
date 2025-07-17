<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingCourseResource;
use App\Models\TrainingCourse;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function getAnnouncedCourse(): JsonResponse
    {
        $course = TrainingCourse::where('is_announced', true)
            ->withCount('votes')
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد دورة معلنة حالياً'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TrainingCourseResource($course)
        ]);
    }
}
