<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VolunteerApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer|min:16|max:100',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'skills' => 'required|string',
            'interests' => 'required|in:Educational,Medicine,Organizational,Media,technical',
            'available_times' => 'required|array|min:1',
            'available_times.*' => 'string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $application = VolunteerApplication::create([
            'full_name' => $request->full_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'skills' => $request->skills,
            'interests' => $request->interests,
            'available_times' => $request->available_times,
            'status' => 'new',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volunteer application submitted successfully!',
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
}
