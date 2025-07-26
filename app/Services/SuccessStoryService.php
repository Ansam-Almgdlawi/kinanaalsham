<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\SuccessStoryRepository;
use Illuminate\Http\UploadedFile;

class SuccessStoryService
{

    public function __construct(protected SuccessStoryRepository $repo) {}

    public function submit(array $data, ?UploadedFile $image, User $user)
    {
        $data['submitted_by_user_id'] = $user->id;
        $data['submission_date'] = now();

        if ($image) {
            $data['image_url'] = $image->store('stories', 'public');
        }

        return $this->repo->create($data);
    }

    public function updateStatus($id, string $status)
    {
        $story = $this->repo->find($id);
        $this->repo->update($story, ['status' => $status]);
        return $story;
    }
}
