<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Role;
use App\Models\VolunteerApplication;
use App\Models\VolunteerDetail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * Create a new user from an accepted volunteer application.
     *
     * @param array $data
     * @param int $volunteerRoleId
     * @return User
     */
    public function createVolunteerUser(
        array $data,
        VolunteerApplication $application,
        int $volunteerRoleId,
        ?UploadedFile $profilePicture = null
    ): User
    {
        $profilePicturePath = null;

        if ($profilePicture) {
            $profilePicturePath = $profilePicture->store('profile_pictures', 'public');
        }

        $user = User::create([
            'name' => $application->full_name,
            'email' => $application->email,
            'password' => Hash::make($data['password']),
            'fcm_token' => $data['fcm_token'] ?? null,
            'phone_number' => $application->phone_number,
            'role_id' => $volunteerRoleId,
            'status' => 'active',
            'profile_picture_url' => $profilePicturePath,
        ]);

        VolunteerDetail::create([
            'user_id' => $user->id,
            'skills' => $application->skills,
            'interests' => $application->interests,
            'availability_schedule' => json_encode($application->available_times),
            'profile_picture_url' => $profilePicturePath,
            'date_joined_from_form' => now(),
        ]);

        return $user;
    }


    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
