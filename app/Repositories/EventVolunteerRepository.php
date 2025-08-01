<?php



// app/Repositories/EventVolunteerRepository.php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Event;
use Exception;

class EventVolunteerRepository
{
    public function register(array $data)
    {
        // 1. التحقق من عدم التسجيل المسبق
        $existingRegistration = DB::table('event_volunteer')
            ->where('event_id', $data['event_id'])
            ->where('user_id', $data['user_id'])
            ->exists();

        if ($existingRegistration) {
            throw new Exception('أنت مسجل مسبقًا في هذه الفعالية.');
        }

        // 2. جلب بيانات الفعالية للتحقق من العدد المسموح
        $event = Event::find($data['event_id']);

        // 3. التحقق من اكتمال العدد (إذا كان `max_participants` محددًا)
        if ($event->max_participants !== null) {
            $currentParticipants = DB::table('event_volunteer')
                ->where('event_id', $data['event_id'])
                ->where('status', 'approved') // نستخدم 'approved' بدل 'registered'
                ->count();

            if ($currentParticipants >= $event->max_participants) {
                throw new Exception('اكتمل العدد المسموح للمتطوعين في هذه الفعالية.');
            }
        }

        // 4. إنشاء التسجيل إذا كل الشروط مطابقة
        return DB::table('event_volunteer')->insertGetId([
            'event_id' => $data['event_id'],
            'user_id' => $data['user_id'],
            'user_type' => $data['user_type'] ?? 'volunteer', // قيمة افتراضية
            'status' => 'pending', // حالة افتراضية
            'registered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }



    // app/Repositories/EventVolunteerRepository.php

    public function getVolunteerEvents($userId)
    {
        return DB::table('event_volunteer')
            ->join('events', 'event_volunteer.event_id', '=', 'events.id')
            ->where('event_volunteer.user_id', $userId)
            ->select(
                'events.id',
                'events.name',
                'events.description',
                'events.start_datetime',
                'events.end_datetime',
                'event_volunteer.status',
                'event_volunteer.registered_at'
            )
            ->get();
    }

}
