<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $inquiry = Inquiry::create([
            'sender_user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        return response()->json(['message' => 'تم إرسال استفسارك بنجاح.', 'data' => $inquiry], 201);
    }

    // 2. للمستفيد: عرض جميع استفساراته
    public function indexBeneficiary()
    {
        $inquiries = Inquiry::with('sender:id,name,email')
        ->where('sender_user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $inquiries]);
    }

    // 3. للمستفيد: عرض استفسار معين (مع التحقق من الملكية)
    public function showBeneficiary(Inquiry $inquiry)
    {
        if ($inquiry->sender_user_id !== Auth::id()) {
            return response()->json(['message' => 'غير مصرح لك بعرض هذا الاستفسار.'], 403);
        }
        return response()->json(['data' => $inquiry]);
    }

    // 4. للأدمن: عرض جميع الاستفسارات
    public function indexAdmin()
    {
        $inquiries = Inquiry::with('sender:id,name,email') // لجلب بيانات المرسل
        ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['data' => $inquiries]);
    }

    // 5. للأدمن: الرد على استفسار
    public function reply(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:5000',
        ]);

        $inquiry->update([
            'admin_reply' => $validated['admin_reply'],
            'replied_at' => now(),
        ]);

        return response()->json(['message' => 'تم إرسال الرد بنجاح.', 'data' => $inquiry]);
    }
}
