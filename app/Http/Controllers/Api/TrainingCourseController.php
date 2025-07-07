<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingCourseRequest;
use App\Http\Resources\TrainingCourseResource;
use App\Models\TrainingCourse;
use App\Services\TrainingCourseService;

class TrainingCourseController extends Controller
{
    protected $service;

    public function __construct(TrainingCourseService $service)
    {
        $this->service = $service;
    }

    public function store(TrainingCourseRequest $request)
    {
        $course = TrainingCourse::create($request->validated());

        return response()->json([
            'success' => true,

            'message' => 'Course created successfully'
        ], 201);
    }
}
