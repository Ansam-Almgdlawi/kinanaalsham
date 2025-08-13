<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectRatingController extends Controller
{
    /**
     * إضافة أو تحديث تقييم المشروع الربحي
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500'
        ]);

        $rating = $project->ratings()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
                'rated_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل التقييم بنجاح',
            'average_rating' => $project->fresh()->average_rating,
            'rating' => $rating
        ], 201);
    }

    /**
     * الحصول على تعليقات المشروع الربحي
     */
    public function getComments(Project $project)
    {
        $comments = $project->ratings()
            ->whereNotNull('comment')
            ->with('user:id,name')
            ->latest()
            ->get(['user_id', 'rating', 'comment', 'rated_at'])
            ->map(function ($rating) {
                return [
                    'user_name' => $rating->user->name,
                    'comment' => $rating->comment,
                    'date' => $rating->rated_at->format('Y-m-d H:i')
                ];
            });

        return response()->json([
            'success' => true,
            'comments' => $comments,
            'total_comments' => $comments->count()
        ]);
    }

    /**
     * الحصول على متوسط تقييم المشروع الربحي
     */
    public function getAverageRating(Project $project)
    {
        $average = $project->ratings()
            ->selectRaw('AVG(rating) as average, COUNT(*) as count')
            ->first();

        return response()->json([
            'success' => true,
            'average_rating' => round($average->average, 1),
            'total_ratings' => $average->count,
            'rating_distribution' => $this->getRatingDistribution($project)
        ]);
    }

    /**
     * الحصول على توزيع التقييمات
     */
    private function getRatingDistribution(Project $project)
    {
        return $project->ratings()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->rating => $item->count];
            });
    }
}
