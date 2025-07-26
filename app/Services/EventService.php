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
}
