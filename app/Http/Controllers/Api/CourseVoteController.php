<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseVoteRequest;
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

        //`1dd(Auth::user());
        $result = $this->voteService->vote($courseId);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        return new CourseVoteRequest($result);
    }

    public function index(): JsonResponse
    {
        $courses = $this->voteService->getAllCoursesWithVotes();

        return response()->json([
            'success' => true,
            'data' => $courses,
            'message' => 'تم جلب جميع الدورات مع تصويتاتها'
        ]);
    }
}
