<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventPostRequest;
use App\Models\Event;
use App\Models\EventPost;
use Illuminate\Http\Request;

class EventPostController extends Controller
{

    public function store(CreateEventPostRequest $request)
    {


        $event = Event::findOrFail($request->event_id);
            // رفع الملفات إذا وجدت
            $mediaPaths = [];
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $mediaPaths[] = $file->store('event_posts', 'public');
                }
            }
        $validatedData = $request->validate([
            'event_id' => 'required|exists:events,id',
            'content' => 'required|string|min:3', // تأكد من وجود محتوى
        ]);

            $post = EventPost::create([
                'event_id' => $event->id,
                'admin_id' => auth()->id(),
                'content' => $validatedData['content'],
                'media' => $mediaPaths ?: null
            ]);

            return response()->json([
                'success' => true,
                'data' => $post
            ], 201);


    }
}
