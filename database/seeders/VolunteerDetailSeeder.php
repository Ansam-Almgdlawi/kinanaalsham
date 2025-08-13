<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VolunteerDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VolunteerDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role_id', '5')->get();

        foreach ($users as $user) {
            VolunteerDetail::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
