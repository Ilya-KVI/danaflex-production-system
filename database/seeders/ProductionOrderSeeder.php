<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionOrder;

class ProductionOrderSeeder extends Seeder
{
    public function run(): void
    {
        ProductionOrder::create([
            'customer_name' => 'ООО Альфа',
            'material' => 'BOPP пленка',
            'quantity' => 5000,
            'status' => 'В производстве'
        ]);


        ProductionOrder::create([
            'customer_name' => 'ООО ТехПак',
            'material' => 'Полиэтиленовая пленка',
            'quantity' => 3000,
            'status' => 'Новый'
        ]);


        ProductionOrder::create([
            'customer_name' => 'ООО ФлексПро',
            'material' => 'Ламинированная упаковка',
            'quantity' => 7500,
            'status' => 'Готово'
        ]);
    }
}
