<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDistribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonthlyDistributionController extends Controller
{
    public function showLatest(Request $request): JsonResponse
    {
        // البحث عن أحدث سجل لتحديد آخر شهر وسنة تم التوزيع فيهما
        $latestEntry = MonthlyDistribution::latest('id')->first();


        if (!$latestEntry) {
            return response()->json([
                'message' => 'No distribution records found yet.',
                'data' => []
            ]);
        }

        // جلب كل سجلات التوزيع لنفس الشهر والسنة
        $distributions = MonthlyDistribution::with('user:id,name,email') // جلب معلومات المستخدم
        ->where('year', $latestEntry->year)
            ->where('month', $latestEntry->month)
            ->orderBy('amount', 'desc')
            ->get();

        $totalAmount = $distributions->sum('amount');

        return response()->json([
            'distribution_period' => [
                'year' => $latestEntry->year,
                'month' => $latestEntry->month,
            ],
            'total_distributed_amount' => $totalAmount,
            'beneficiaries_count' => $distributions->count(),
            'data' => $distributions->map(function ($dist) {
                if ($dist->user) {
                    return [
                        'beneficiary_id' => $dist->user->id,
                        'beneficiary_name' => $dist->user->name,
                        'beneficiary_email' => $dist->user->email,
                        'amount_allocated' => $dist->amount,
                    ];
                }
                return null; // تجاهل السجلات التي قد يكون مستخدمها محذوفاً
            })->filter()->values(),
        ]);
    }
}
