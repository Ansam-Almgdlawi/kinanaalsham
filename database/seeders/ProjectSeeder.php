<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'project_number' => 'PRJ-2025-0001',
                'name' => 'معرض الكتاب السنوي',
                'description' => 'معرض لبيع الكتب بأسعار مخفضة',
                'budget' => 50000.00,
                'objective' => 'جمع تبرعات لصالح المكتبة العامة',
                'status' => 'planning',
                'start_date' => '2025-10-01',
                'end_date' => '2025-10-07',
                'requirements' => 'توفير خيمة كبيرة وطاولات',
                'max_beneficiaries' => 100,
                'current_beneficiaries' => 0,
                'total_revenue' => 0,
                'total_expenses' => 0,
                'created_by_user_id' => 1, // تأكد أن user_id=1 موجود
            ],
            [
                'project_number' => 'PRJ-2025-0002',
                'name' => 'مشروع متجر إلكتروني',
                'description' => 'إنشاء متجر إلكتروني للمنتجات المحلية',
                'budget' => 120000.00,
                'objective' => 'دعم المشاريع الصغيرة وزيادة الإيرادات',
                'status' => 'active',
                'start_date' => '2025-06-01',
                'end_date' => null,
                'requirements' => 'فريق تطوير + استضافة سحابية',
                'max_beneficiaries' => 200,
                'current_beneficiaries' => 50,
                'total_revenue' => 30000.00,
                'total_expenses' => 15000.00,
                'created_by_user_id' => 1,
            ],
        ];

        foreach ($projects as $p) {
            Project::updateOrCreate(['project_number' => $p['project_number']], $p);
        }
    }
}
