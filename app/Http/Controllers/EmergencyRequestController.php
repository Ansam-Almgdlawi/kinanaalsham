<?php

namespace App\Http\Controllers;

use App\Models\EmergencyRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmergencyRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_details' => 'required|string|max:3000',
            'address' => 'required|string|max:255',
            'required_specialization' => ['required', Rule::in(['طبي', 'ميداني', 'استشاري'])],
        ]);

        $emergencyRequest = EmergencyRequest::create([
            'beneficiary_user_id' => Auth::id(),
            'request_details' => $validated['request_details'],
            'address' => $validated['address'],
            'required_specialization' => $validated['required_specialization'],
        ]);

//        $targetAddress = $validated['address'];
//        $volunteersToNotify = User::whereHas('volunteerDetails', function ($query) use ($targetAddress) {
//            // حدد اسم الجدول بشكل صريح: 'volunteer_details.address'
//            $query->where('volunteer_details.address', $targetAddress);
//        })->get();

        // 3. إرسال الإشعارات (Notifications)
        // هذا الجزء مهم جدًا لإعلام المتطوعين بوجود طلب جديد
        // Notification::send($volunteersToNotify, new NewEmergencyRequest($emergencyRequest));

        return response()->json([
            'message' => 'تم إرسال طلب الطوارئ بنجاح وإعلام المتطوعين في منطقتك.',
            'data' => $emergencyRequest
        ], 201);
    }

    public function showMyAreaRequests()
    {
        $volunteer = Auth::user();
        $volunteerAddress = $volunteer->volunteerDetails->address ?? null;

        if (!$volunteerAddress) {
            return response()->json(['message' => 'يرجى تحديد عنوانك في ملفك الشخصي أولاً.'], 400);
        }

        // جلب الطلبات المفتوحة (pending) والتي تطابق عنوان سكن المتطوع
        $requests = EmergencyRequest::with('beneficiary:id,name') // لجلب اسم المستفيد
        ->where('address', $volunteerAddress)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json(['data' => $requests]);
    }
    public function acceptRequest(EmergencyRequest $request)
    {
        DB::beginTransaction();
        try {
            $emergencyRequest = EmergencyRequest::where('id', $request->id)->lockForUpdate()->first();

            if ($emergencyRequest->status !== 'pending') {
                DB::rollBack();
                return response()->json(['message' => 'عذراً، تم قبول هذا الطلب من قبل متطوع آخر.'], 409);
            }

            $emergencyRequest->update([
                'assigned_volunteer_user_id' => Auth::id(),
                'status' => 'in_progress',
               // 'resolution_details'=>,
            ]);

            DB::commit();
            return response()->json(['message' => 'تم قبول الطلب بنجاح.', 'data' => $emergencyRequest]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'حدث خطأ ما، يرجى المحاولة مرة أخرى.'], 500);
        }
    }
    public function showMyEmergencyRequests()
    {
        $user = Auth::user();

        $requests = EmergencyRequest::with([
            'assignedVolunteer:id,name,phone_number', // لجلب اسم ورقم المتطوع
        ])
            ->where('beneficiary_user_id', $user->id)
            ->latest()
            ->get();

        return response()->json(['data' => $requests]);
    }

}
