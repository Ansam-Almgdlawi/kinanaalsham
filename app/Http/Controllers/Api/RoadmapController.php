<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Roadmap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoadmapController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
        ]);
        $roadmap = Roadmap::create([
            'event_id' => $request->event_id,
            'supervisor_id' => Auth::id(),
            'title' => $request->title,
        ]);

        return response()->json($roadmap, 201);
    }

    // Volunteer views roadmaps for an event they are part of
    public function index(Event $event)
    {
        $roadmaps = $event->roadmaps()->with('tasks')->get();
        return response()->json($roadmaps);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
