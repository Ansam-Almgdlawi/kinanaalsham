<?php

namespace App\Repositories;

use App\Models\SuccessStory;

class SuccessStoryRepository
{

    public function create(array $data): SuccessStory
    {
        return SuccessStory::create($data);
    }

    public function getPending()
    {
        return SuccessStory::where('status', 'pending_approval')->with('user')->get();
    }

    public function find($id): ?SuccessStory
    {
        return SuccessStory::findOrFail($id);
    }

    public function update(SuccessStory $story, array $data)
    {
        $story->update($data);
        return $story;
    }
}
