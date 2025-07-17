<?php

namespace Database\Seeders;

use App\Models\BeneficiaryType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeneficiaryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'يتيم',
                'description' => 'طفل فاقد أحد الوالدين أو كلاهما.'
            ],
            [
                'name' => 'مسن',
                'description' => 'شخص كبير في السن ويحتاج إلى رعاية.'
            ],
            [
                'name' => 'أسرة مكفولة',
                'description' => 'عائلة تتلقى دعماً مالياً أو عينياً.'
            ],
        ];

        foreach ($types as $type) {
            BeneficiaryType::updateOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']]
            );
        }
    }
}
