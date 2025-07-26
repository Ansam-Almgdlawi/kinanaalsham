<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HonorBoardController extends Controller
{

    public function show()
    {
        // نستخدم Query Builder للقيام بعمليات متقدمة مثل الربط (join) والترتيب
        $topVolunteers = DB::table('users')
            // 1. نربط جدول users مع volunteer_details
            ->join('volunteer_details', 'users.id', '=', 'volunteer_details.user_id')

            // 2. نختار فقط الحقول التي نحتاجها
            ->select(
                'users.id',
                'users.name',
                'volunteer_details.total_hours_volunteered'
            )

            // 3. نرتب النتائج تنازليًا حسب عدد الساعات
            ->orderBy('volunteer_details.total_hours_volunteered', 'desc')

            // 4. نأخذ فقط أفضل 10 نتائج
            ->limit(10)

            // 5. ننفذ الاستعلام ونجلب النتائج
            ->get()

            // 6. نستخدم دالة map لإضافة حقل "الترتيب" (rank) لكل نتيجة
            ->map(function ($volunteer, $key) {
                $volunteer->rank = $key + 1; // $key يبدأ من 0، لذلك نضيف 1
                return $volunteer;
            });

        // 7. نرجع النتائج كاستجابة JSON
        return response()->json(['data' => $topVolunteers]);
    }
}
