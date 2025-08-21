<?php

// app/Http/Controllers/EventController.php

namespace App\Http\Controllers;

use App\Http\Requests\EventCreateRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\EventPost;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function store(EventCreateRequest $request)
    {
        $event = $this->eventService->createEvent($request);
        return response()->json([
            'message' => 'تم إنشاء الفعالية بنجاح',
            'data' => $event,
        ], 201);
    }

    public function getEventsByMonth(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'sometimes|integer|min:2020'
        ]);

        $month = $request->input('month');
        $year = $request->input('year', date('Y')); // السنة الحالية افتراضياً

        $events = Event::whereMonth('start_datetime', $month)
            ->whereYear('start_datetime', $year)
            ->orderBy('start_datetime')
            ->get();

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }




    public function getEventsByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d' // تنسيق التاريخ: سنة-شهر-يوم
        ]);

        $date = $request->input('date');

        $events = Event::whereDate('start_datetime', $date)
            ->orderBy('start_datetime')
            ->get();

        return response()->json([
            'success' => true,
            'date' => $date,
            'events' => $events
        ]);
    }

    public function getPublishedEvents()
    {
        $events = EventPost::with(['event', 'admin']) // تحميل العلاقات المطلوبة
        ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'events' => $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'content' => $event->content,
                    'media' => $event->media ? asset('storage/' . $event->media[0]) : null,
                    'event_name' => $event->event->name ?? null,
                    'admin_name' => $event->admin->name ?? null,
                    'created_at' => $event->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }
    public function show($id)
    {
        $event = Event::with([
            'eventType',
            'organizer',
            'supervisor',
            'volunteers'
        ])->findOrFail($id);

        return response()->json($event);
    }

}
