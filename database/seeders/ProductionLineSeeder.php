<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionLine;


class ProductionLineSeeder extends Seeder
{

    public function run(): void
    {

        ProductionLine::create([

            'name' => 'Линия №1',

            'temperature' => 42,

            'load_percent' => 75,

            'material' => 'PE-500',

            'status' => 'Работает'

        ]);

    }

}
