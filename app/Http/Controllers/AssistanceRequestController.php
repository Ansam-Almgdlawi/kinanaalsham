<?php

namespace App\Http\Controllers;

use App\Models\AssistanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssistanceRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'assistance_type' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ]);

        $assistanceRequest = AssistanceRequest::create([
            'beneficiary_user_id' => Auth::id(),
            'assistance_type' => $validated['assistance_type'],
            'description' => $validated['description'],
        ]);

        return response()->json(['message' => 'تم إرسال طلب المساعدة بنجاح.', 'data' => $assistanceRequest], 201);
    }

    // 2. للمستفيد: عرض طلباته
    public function index()
    {
        $requests = AssistanceRequest::where('beneficiary_user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['data' => $requests]);
    }

    // 3. للأدمن: عرض جميع الطلبات
    public function indexAdmin()
    {
        $requests = AssistanceRequest::with('beneficiary:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['data' => $requests]);
    }

    // 4. للأدمن: تحديث حالة الطلب
    public function updateStatus(Request $request, AssistanceRequest $assistanceRequest)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'in_progress'])],
            'admin_notes' => 'nullable|string',
        ]);
        $assistanceRequest->update($validated);

        return response()->json(['message' => 'تم تحديث حالة الطلب بنجاح.']);
    }
}
