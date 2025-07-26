<?php

namespace App\Http\Controllers;

use App\Models\VolunteerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'availability_schedule' => 'nullable|array',
            'availability_schedule.*' => 'string|max:100',
        ]);
        $volunteerDetails = VolunteerDetail::updateOrCreate(
            ['user_id' => $user->id],
            $validatedData
        );

        return response()->json([
            'message' => 'تم تحديث ملفك الشخصي بنجاح.',
            'data' => $volunteerDetails
        ]);
    }
    public function show()
    {
        $user = Auth::user()->load([
            'volunteerDetails',
            'role', // لو عندك علاقة مع جدول الأدوار
            // أضف علاقات أخرى مثل:
            // 'volunteerDetails.experiences',
            // 'volunteerDetails.education',
        ]);

        if (!$user->volunteerDetails) {
            return response()->json(['message' => 'لم يتم العثور على تفاصيل الملف الشخصي. يرجى تحديث بياناتك.'], 404);
        }

        return response()->json([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role->name ?? null,
                'volunteer_details' => $user->volunteerDetails,
            ]
        ]);
    }

}
