<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        // التأكد أن المستخدم هو أدمن (مثلاً role_id = 1)
        if ($user->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية الدخول كأدمن.',
            ], 403);
        }

        // إنشاء التوكن
        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token' => $token,
        ]);
    }

    public function loginProjectManager(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        // التأكد أن المستخدم هو Project Manager (مثلاً role_id = 2)
        if ($user->role_id !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية الدخول كمدير مشروع.',
            ], 403);
        }

        // إنشاء التوكن
        $token = $user->createToken('project_manager_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }
}
