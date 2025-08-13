<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectBeneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectBeneficiaryController extends Controller
{


    public function register(Request $request, $projectId)
    {
        // البحث عن المشروع أولاً
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'المشروع غير موجود'
            ], 404);
        }

        // التحقق من أن المستخدم مسجل دخول
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }
        if ($user->role_id !== 6) {
            return response()->json([
                'success' => false,
                'message' => 'عفواً، التسجيل في الفعاليات متاح فقط للمستفيدين المسجلين'
            ], 403);
        }
        // التحقق من عدم التسجيل المسبق
        if ($project->beneficiaries()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'أنت مسجل بالفعل في هذا المشروع'
            ], 400);
        }

        // التحقق من توفر المقاعد
        $current_beneficiaries = $project->beneficiaries()->count();
        if ($project->max_beneficiaries > 0 && $current_beneficiaries >= $project->max_beneficiaries) {
            return response()->json([
                'success' => false,
                'message' => 'تم الوصول إلى العدد الأقصى للمستفيدين'
            ], 400);
        }

        // التحقق من صحة البيانات
        $request->validate([
            'benefit_details' => 'required|string|max:500'
        ]);

        DB::transaction(function () use ($project, $user, $request) {
            // تسجيل المستفيد
            $project->beneficiaries()->create([
                'user_id' => $user->id,
                'benefit_details' => $request->benefit_details,
                'registered_at' => now()
            ]);

            if ($project->max_beneficiaries > 0) {
                $project->increment('current_beneficiaries');
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل بنجاح',
            'data' => [
                'remaining_slots' => $project->max_beneficiaries > 0
                    ? $project->max_beneficiaries - $current_beneficiaries - 1
                    : 'غير محدود'
            ]
        ]);
    }}
