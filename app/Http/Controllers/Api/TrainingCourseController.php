<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingCourseRequest;
use App\Http\Resources\TrainingCourseResource;
use App\Models\TrainingCourse;
use App\Models\User;
use App\Services\TrainingCourseService;

class TrainingCourseController extends Controller
{
    protected $service;

    public function __construct(TrainingCourseService $service)
    {
        $this->service = $service;
       // $this->middleware(['role:admin|Project Manager']);
    }

    public function store(TrainingCourseRequest $request)
    {

        // التحقق من الصلاحيات يدوياً (طبقة حماية إضافية)
        $user = auth()->user();

        if (!$user || !in_array($user->role->id, [1,2])) {
            abort(403, 'هذا الإجراء مسموح فقط للادمن والبروجيكت مانجر!');
        }


        $course = TrainingCourse::create($request->validated());

        return response()->json([
            'success' => true,

            'message' => 'Course created successfully'
        ], 201);
    }


}
