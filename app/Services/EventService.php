<?php

// app/Services/EventService.php

namespace App\Services;

use App\Http\Requests\EventCreateRequest;
use App\Repositories\EventRepository;
use App\Http\Resources\EventResource;

class EventService
{
    protected $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function createEvent(EventCreateRequest $request)
    {
        $organizer = $request->user(); // الأدمن المنظم
        $event = $this->eventRepository->create($request->validated(), $organizer);

        return new EventResource($event);
    }

    // app/Services/EventVolunteerService.php

    public function registerVolunteer(RegisterVolunteerRequest $request)
    {
        $data = $request->validated();

        try {
            $registration = $this->eventVolunteerRepo->register($data);
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيلك بنجاح في الفعالية.',
                'data' => new EventVolunteerResource($registration),
                'available_slots' => $this->getRemainingSlots($data['event_id']), // عدد الأماكن المتبقية
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'available_slots' => $this->getRemainingSlots($data['event_id']),
            ], 400);
        }
    }

// دالة لحساب الأماكن المتبقية
    private function getRemainingSlots($eventId)
    {
        $event = \App\Models\Event::find($eventId);
        if ($event->max_participants === null) return 'غير محدود';

        $currentParticipants = \App\Models\EventVolunteer::where('event_id', $eventId)
            ->where('registration_status', 'registered')
            ->count();
        return $event->max_participants - $currentParticipants;
    }

}
