<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuccessStoryRequest;
use App\Http\Resources\SuccessStoryResource;
use App\Models\SuccessStory;
use App\Repositories\SuccessStoryRepository;
use App\Services\SuccessStoryService;
use Illuminate\Http\Request;

class SuccessStoryController extends Controller
{
    public function __construct(protected SuccessStoryService $service) {}

    public function store(StoreSuccessStoryRequest $request)
    {
        $story = $this->service->submit(
            $request->validated(),
            $request->file('image'),
            auth()->user()
        );

        return new SuccessStoryResource($story);
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'is_featured' => 'nullable|boolean',
        ]);

        $story = SuccessStory::findOrFail($id);
        $story->status = $request->status;

        if ($request->has('is_featured')) {
            $story->is_featured = $request->is_featured;
        }

        $story->save();

        return response()->json([
            'message' => 'Story status updated successfully.',
            'story' => $story,
        ]);
    }


    public function pending()
    {
        $stories = (new SuccessStoryRepository())->getPending();
        return SuccessStoryResource::collection($stories);
    }

    public function show($id)
    {
        $story = (new SuccessStoryRepository())->find($id);
        return new SuccessStoryResource($story->load('user'));
    }
    public function approvedStories()
    {
        $stories = SuccessStory::where('status', 'approved')
            ->orderByDesc('is_featured')
            ->orderByDesc('submission_date')
            ->get();

        return response()->json($stories);
    }

}
