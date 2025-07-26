<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // صلاحيات الإدارة
            'إثراء',
            'تتبع المقربات',
            'تواريخ إشراف',
            'إثباتات رفع',
            'إدارة الأساس',
            'التعبئة',
            'إدارة التعاريض',

            // صلاحيات خاصة
            'رفض القسم',
            'مراجعة العمال',
            'إدارة الملاحظات',

            // صلاحيات أساسية
            'التطوع',
            'طلب الخدمات'
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }
    }
}
