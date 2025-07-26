<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminProjectManager
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


        $allowedRoles = [1, 2];

        if (!in_array($user->role_id, $allowedRoles)) {
            return response()->json(['message' => 'هذا الإجراء مسموح فقط للادمن والبروجيكت مانجر!'], 403);
        }

        return $next($request);
    }
}
