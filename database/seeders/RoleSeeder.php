<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Admin',

            ],
            [
                'name' => 'ProjectManager',

            ],
            [
                'name' => 'Supervisor',

            ],
            [
                'name' => 'Coordinator',

            ],
            [
                'name' => 'Volunteer',

            ],
            [
                'name' => 'Beneficiary',

            ]
        ];

        foreach ($roles as $role) {
            $existingRole = Role::where('name', $role['name'])->first();

            if ($existingRole) {
                $existingRole->update($role);
            } else {
                Role::create($role);
            }
        }
    }
}
