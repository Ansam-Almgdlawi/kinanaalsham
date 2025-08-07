<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VolunteerBadgeController extends Controller
{
    public function volunteerOfTheWeek(): JsonResponse
    {
        return $this->getTopVolunteer('week');
    }

    public function volunteerOfTheMonth(): JsonResponse
    {
        return $this->getTopVolunteer('month');
    }

    private function getTopVolunteer(string $period): JsonResponse
    {
        $fromDate = $period === 'week'
            ? Carbon::now()->subDays(7)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $toDate = Carbon::now()->endOfDay();

        $volunteer = DB::table('event_volunteer')
            ->join('users', 'event_volunteer.user_id', '=', 'users.id')
            ->join('events', 'event_volunteer.event_id', '=', 'events.id')
            ->where('event_volunteer.status', 'attended')
            ->where('event_volunteer.user_type', 'volunteer')
            ->whereBetween('events.end_datetime', [$fromDate, $toDate])
            ->select(
                'event_volunteer.user_id',
                'users.name',
                DB::raw('SUM(TIMESTAMPDIFF(SECOND, events.start_datetime, events.end_datetime)) / 3600 as total_hours')
            )
            ->groupBy('event_volunteer.user_id', 'users.name')
            ->orderByDesc('total_hours')
            ->first();

        if (!$volunteer || $volunteer->total_hours == 0) {
            return response()->json([
                'message' => 'لا يوجد متطوع حصل على شارة خلال هذه الفترة.'
            ], 404);
        }

        return response()->json([
            'user_id' => $volunteer->user_id,
            'name' => $volunteer->name,
            'total_hours' => round($volunteer->total_hours, 2),
            'period' => $period === 'week' ? 'الأسبوع' : 'الشهر',
            'badge' => $period === 'week' ? 'شارة متطوع الأسبوع' : 'شارة متطوع الشهر',
        ]);
    }
}
