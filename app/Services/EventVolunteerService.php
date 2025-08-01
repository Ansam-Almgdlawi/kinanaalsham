<?php

namespace App\Services;

use App\Http\Requests\RegisterVolunteerRequest;
use App\Repositories\EventVolunteerRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

class EventVolunteerService
{
    protected $eventVolunteerRepo;

    public function __construct(EventVolunteerRepository $eventVolunteerRepo)
    {
        $this->eventVolunteerRepo = $eventVolunteerRepo;
    }

    public function registerVolunteer(array $data)
    {
        try {
            $registrationId = $this->eventVolunteerRepo->register([
                'event_id' => $data['event_id'],
                'user_id' => $data['user_id'],
                'user_type' => 'volunteer',
                'status' => 'registered'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم التسجيل بنجاح',
                'registration_id' => $registrationId
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function getRemainingSlots($eventId)
    {
        $event = Event::find($eventId);
        if ($event->max_participants === null) return 'غير محدود';

        $currentParticipants = DB::table('event_volunteer')
            ->where('event_id', $eventId)
            ->where('status', 'registered') // نتحقق من الحالات المسجلة
            ->count();

        return $event->max_participants - $currentParticipants;
    }



    public function getVolunteerEvents($userId)
    {
        try {
            $events = $this->eventVolunteerRepo->getVolunteerEvents($userId);

            return response()->json([
                'success' => true,
                'data' => $events
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
