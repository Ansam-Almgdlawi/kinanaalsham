<?php

// app/Http/Controllers/EventController.php

namespace App\Http\Controllers;

use App\Http\Requests\EventCreateRequest;
use App\Models\Event;
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
}
