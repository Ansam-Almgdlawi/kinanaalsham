<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $warehouses = [
            ['name' => 'المستودع الرئيسي للمواد الغذائية', 'category' => 'food'],
            ['name' => 'المستودع الرئيسي للملابس', 'category' => 'clothes'],
            ['name' => 'المستودع الرئيسي للتدفئة', 'category' => 'heating'],
            ['name' => 'المستودع الرئيسي للأدوية', 'category' => 'Medicines'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(['name' => $warehouse['name']], $warehouse);
        }
    }
}
