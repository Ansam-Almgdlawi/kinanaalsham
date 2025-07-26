<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // صلاحيات المدير
        $admin = Role::findByName('Admin');
        $admin->givePermissionTo(Permission::all());

        // صلاحيات مدير المشروع
        $pm = Role::findByName('ProjectManager');
        $pm->givePermissionTo([
            'إدارة الأساس',
            'التعبئة',
            'إدارة التعاريض',
            'تواريخ إشراف'
        ]);

        // صلاحيات المشرف
        $supervisor = Role::findByName('Supervisor');
        $supervisor->givePermissionTo([
            'مراجعة العمال',
            'تتبع المقربات',
            'إدارة الملاحظات'
        ]);

        // صلاحيات المنسق
        $coordinator = Role::findByName('Coordinator');
        $coordinator->givePermissionTo([
            'إثباتات رفع',
            'تواريخ إشراف'
        ]);

        // صلاحيات المتطوع
        $volunteer = Role::findByName('Volunteer');
        $volunteer->givePermissionTo('التطوع');
    }
}
