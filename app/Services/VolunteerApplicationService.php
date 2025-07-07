<?php

namespace App\Services;

use App\Models\VolunteerApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class VolunteerApplicationService
{
    public function store(array $data, ?UploadedFile $cv = null): VolunteerApplication
    {
        if ($cv) {
            $path = $cv->store('cvs', 'public');
            $data['cv_path'] = $path;
        }

        return VolunteerApplication::create($data);
    }
}
