<?php

namespace App\Repositories;

use App\Models\TrainingCourse;
use Illuminate\Pagination\LengthAwarePaginator;

class TrainingCourseRepository
{
    protected $model;

    public function __construct(TrainingCourse $model)
    {
        $this->model = $model;
    }

    public function getAllCourses(): LengthAwarePaginator
    {
        return $this->model->with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }
}
