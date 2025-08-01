<?php

namespace App\Repositories;

use App\Models\CourseVote;
use App\Models\TrainingCourse;
use App\Models\User;
class
CourseVoteRepository
{
    public function createVote(TrainingCourse $course, User $user): CourseVote|\Illuminate\Http\JsonResponse
    {


        // تحقق من عدم التصويت المسبق
        if ($course->votes()->where('user_id', $user->id)->exists()) {
            return response()->json('المستخدم قام بالتصويت مسبقاً');
        }

        return $course->votes()->create([
            'user_id' => $user->id,
            'voted_at' => now()
        ]);
    }

    public function getCourseWithVotes(int $courseId)
    {
        return TrainingCourse::withCount(['votes as total_votes'])->with('votes')
            ->findOrFail($courseId);
    }
}
