<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVolunteerApplicationRequest;
use App\Http\Resources\VolunteerApplicationResource;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Services\VolunteerApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class VolunteerApplicationController extends Controller
{
    public function __construct(protected VolunteerApplicationService $service) {}

    public function store(StoreVolunteerApplicationRequest $request)
    {
        $application = $this->service->store(
            $request->validated(),
            $request->file('cv')
        );

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'data' => new VolunteerApplicationResource($application),
        ], 201);
    }

    public function index()
    {
        $applications = VolunteerApplication::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }


    public function show($id)
    {
        $application = VolunteerApplication::findOrFail($id);
        return response()->json($application);
    }

    public function updateStatus(Request $request, $id)
    {
        $application = VolunteerApplication::findOrFail($id);

        $request->validate([
            'status' => 'required|in:new,reviewed,accepted,rejected',

        ]);

        $application->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully.',
        ]);
    }
    public function showProfilePicture(int $id)
    {
        $user = User::findOrFail($id);

        if (!$user->profile_picture_url || !Storage::disk('public')->exists($user->profile_picture_url)) {
            return response()->json([
                'success' => false,
                'message' => 'الصورة غير موجودة.'
            ], 404);
        }

        // جلب المسار الفعلي
        $path = Storage::disk('public')->path($user->profile_picture_url);

        // إرجاع الصورة كرد من نوع image/*
        return response()->file($path);
    }
}
