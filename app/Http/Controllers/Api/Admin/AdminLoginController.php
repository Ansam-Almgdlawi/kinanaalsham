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

        // السماح فقط للأدوار: 1 (أدمن)، 2 (PM)، 3 (Volunteer Manager)، 4 (Finance Manager)
        if (!in_array($user->role_id, [1, 2, 3, 4])) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية الدخول للنظام.',
            ], 403);
        }

        // إنشاء التوكن
        $token = $user->createToken('role_'.$user->role_id.'_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token'   => $token,
            'role_id' => $user->role_id,
            'role'    => $user->role ? $user->role->name : null,
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
