<?php

namespace Database\Seeders;

use App\Models\ProductionLog;
use Illuminate\Database\Seeder;

class ProductionLogSeeder extends Seeder
{
    public function run(): void
    {
        ProductionLog::create([
            'message' => '🚀 Производственная система запущена'
        ]);

        ProductionLog::create([
            'message' => '🏭 Производственные линии готовы к работе'
        ]);
    }
}
