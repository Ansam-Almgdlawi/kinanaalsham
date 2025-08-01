<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventRatingController extends Controller
{

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500'
        ]);

        $rating = $event->ratings()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['rating' => $validated['rating'], 'comment' => $validated['comment']]
        );

        return response()->json([
            'success' => true,
            'average_rating' => $event->fresh()->averageRating(),
            'rating' => $rating
        ], 201);
    }


    public function getComments(Event $event)
    {
        $comments = $event->ratings()
            ->whereNotNull('comment') // فقط التقييمات التي تحتوي على تعليق
            ->with('user:id,name') // جلب اسم المستخدم فقط
            ->latest()
            ->get(['user_id', 'comment', 'created_at'])
            ->map(function ($rating) {
                return [
                    'user_name' => $rating->user->name,
                    'comment' => $rating->comment,
                    'date' => $rating->created_at->format('Y-m-d H:i')
                ];
            });

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    // app/Http/Controllers/EventRatingController.php
    public function getAverageRating(Event $event)
    {
        $average = $event->ratings()
            ->selectRaw('AVG(rating) as average, COUNT(*) as count')
            ->first();

        return response()->json([
            'success' => true,
            'average_rating' => round($average->average, 1),
            'total_ratings' => $average->count,
            'details' => $this->getRatingDistribution($event)
        ]);
    }

    private function getRatingDistribution(Event $event)
    {
        return $event->ratings()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->rating => $item->count];
            });
    }

}
