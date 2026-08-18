<?php

namespace App\Http\Controllers;

use App\Models\ProductionLine;
use App\Models\ProductionLog;

class ProductionLineController extends Controller
{
    public function start($id)
    {
        $line = ProductionLine::findOrFail($id);

        if ($line->status === 'Работает') {
            return back();
        }

        $line->update([
            'status' => 'Работает',
            'temperature' => rand(38, 48),
            'load_percent' => rand(65, 95),
        ]);

        ProductionLog::create([
            'message' => "{$line->name} запущена. "
                . "Температура: {$line->temperature}°C, "
                . "загрузка: {$line->load_percent}%.",
        ]);

        return back();
    }


    public function stop($id)
    {
        $line = ProductionLine::findOrFail($id);

        if ($line->status === 'Остановлена') {
            return back();
        }

        $previousTemperature = $line->temperature;
        $previousLoad = $line->load_percent;

        $line->update([
            'status' => 'Остановлена',
            'temperature' => 25,
            'load_percent' => 0,
        ]);

        ProductionLog::create([
            'message' => "{$line->name} остановлена. "
                . "Перед остановкой: {$previousTemperature}°C, "
                . "загрузка {$previousLoad}%.",
        ]);

        return back();
    }
}
