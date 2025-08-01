<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBeneficiaryVolunteer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        // 1. تحقق من تسجيل الدخول أولاً
        if (!$user) {
            return response()->json(['message' => 'يجب تسجيل الدخول أولاً'], 401);
        }

        // 2. تحقق من نوع المستخدم (باستخدام role_id كما في جدولك)
        $allowedRoles = [5, 6]; // 2 = متطوع، 3 = مستفيد (عدل حسب جدولك)

        if (!in_array($user->role_id, $allowedRoles)) {
            return response()->json(['message' => 'هذا الإجراء مسموح فقط للمستفيدين والمتطوعين!'], 403);
        }

        return $next($request);
    }
}
