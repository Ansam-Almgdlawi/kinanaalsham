<?php

// app/Repositories/EventRepository.php

namespace App\Repositories;

use App\Models\Event;
use App\Models\User;

class EventRepository
{
    public function create(array $data, User $organizer)
    {
        return Event::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'event_type_id' => $data['event_type_id'],
            'start_datetime' => $data['start_datetime'],
            'end_datetime' => $data['end_datetime'],
            'location_text' => $data['location_text'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'status' => 'planned',
            'organizer_user_id' => $organizer->id,
            'supervisor_user_id' => $data['supervisor_user_id'] ?? null,
            'target_audience' => $data['target_audience'] ?? null,
            'max_participants' => $data['max_participants'] ?? null,
            'is_public' => $data['is_public'],
        ]);
    }




}
