<?php

namespace App\Services;

use App\Repositories\TrainingCourseRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\TrainingCourseResource;
class TrainingCourseService
{
    protected $repository;

    public function __construct(TrainingCourseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllCourses(): AnonymousResourceCollection
    {
        $courses = $this->repository->getAllCourses();
        return TrainingCourseResource::collection($courses);
    }
}
