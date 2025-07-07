<?php

namespace App\Repositories;

use App\Models\VolunteerApplication;

class VolunteerApplicationRepository
{
    /**
     * Find an accepted volunteer application by email.
     *
     * @param string $email
     * @return VolunteerApplication|null
     */
    public function findAcceptedByEmail(string $email): ?VolunteerApplication
    {
        return VolunteerApplication::where('email', $email)
            ->where('status', 'accepted')
            ->first();
    }


    /**
     * Check if an email exists in accepted volunteer applications.
     *
     * @param string $email
     * @return bool
     */
    public function isEmailAccepted(string $email): bool
    {
        return VolunteerApplication::where('email', $email)
            ->where('status', 'accepted')
            ->exists();
    }
}
