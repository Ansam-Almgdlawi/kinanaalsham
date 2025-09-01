<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDistribution;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function destroy(User $user): JsonResponse
    {
        if (Auth::id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك حذف حسابك الخاص.'
            ], 403); // 403 Forbidden
        }
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'تم حذف المستخدم بنجاح.'
        ]);
    }
    public function index(): JsonResponse
    {

        $volunteers = User::where('role_id', 5)
        ->with('events')
            ->get();

        $formattedVolunteers = $volunteers->map(function ($volunteer) {

            $totalHours = $volunteer->events->reduce(function ($carry, $event) {
                $start = Carbon::parse($event->start_datetime);
                $end = Carbon::parse($event->end_datetime);

                return $carry + $start->diffInHours($end);
            }, 0);

            return [
                'id' => $volunteer->id,
                'name' => $volunteer->name,
                'email' => $volunteer->email,
                'total_volunteer_hours' => $totalHours,
                'attended_events' => $volunteer->events->map(function ($event) {
                    return [
                        'event_name' => $event->name,
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'volunteers' => $formattedVolunteers
        ]);
    }
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
