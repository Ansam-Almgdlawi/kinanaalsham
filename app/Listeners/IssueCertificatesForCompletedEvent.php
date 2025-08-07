<?php

namespace App\Listeners;

use App\Events\EventCompleted;
use App\Models\Certificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class IssueCertificatesForCompletedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EventCompleted $eventCompleted)
    {
        $event = $eventCompleted->event;

        $attendedVolunteers = DB::table('event_volunteer')
            ->where('event_id', $event->id)
            ->where('status', 'attended')
            ->pluck('user_id');

        foreach ($attendedVolunteers as $userId) {
            Certificate::updateOrCreate(
                ['user_id' => $userId, 'event_id' => $event->id],
                ['issued_at' => now()]
            );
        }
    }
}
