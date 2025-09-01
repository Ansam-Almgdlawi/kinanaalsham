<?php

namespace Database\Seeders;

use App\Models\Opportunity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpportunitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opportunities = [
            [
                'title' => 'Teaching Assistant for Kids',
                'description' => 'Help children in after-school programs with basic subjects.',
                'type' => 'volunteering',
                'status' => 'open',
                'location_text' => 'Damascus',
                'start_date' => now()->addDays(5),
                'end_date' => now()->addMonths(1),
                'requirements' => 'Good communication and patience with children.',
                'skills' => 'Teaching, Communication, Patience',
                'category' => 'Educational',
                'is_remote' => false,
            ],
            [
                'title' => 'Medical Camp Volunteer',
                'description' => 'Assist doctors and organize patients during free medical checkups.',
                'type' => 'volunteering',
                'status' => 'open',
                'location_text' => 'Aleppo',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addMonths(2),
                'requirements' => 'Basic medical knowledge and organizational skills.',
                'skills' => 'First Aid, Organization',
                'category' => 'Medicine',
                'is_remote' => false,
            ],
            [
                'title' => 'Web Developer Intern',
                'description' => 'Assist in building web apps for the NGO using Laravel and React.',
                'type' => 'job',
                'status' => 'open',
                'location_text' => 'Remote',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addMonths(3),
                'requirements' => 'Knowledge of Laravel or React is a plus.',
                'skills' => 'Laravel, React, PHP',
                'category' => 'Technical',
                'is_remote' => true,
            ],
            [
                'title' => 'Social Media Coordinator',
                'description' => 'Manage social media pages and create engaging posts.',
                'type' => 'job',
                'status' => 'open',
                'location_text' => 'Remote',
                'start_date' => now()->addDays(3),
                'end_date' => now()->addMonths(6),
                'requirements' => 'Experience with social media platforms.',
                'skills' => 'Communication, Social Media, Creativity',
                'category' => 'Media',
                'is_remote' => true,
            ],
        ];

        foreach ($opportunities as $opportunity) {
            Opportunity::create($opportunity);
        }
    }
}
