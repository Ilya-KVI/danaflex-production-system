<?php

namespace App\Http\Controllers;

use App\Models\ProductionLog;
use App\Models\ProductionLine;
use Illuminate\Http\Request;

class ProductionLineController extends Controller
{
    public function start($id)
    {
        $line = ProductionLine::findOrFail($id);

        $line->update([
            'status' => 'Работает',
            'temperature' => rand(38, 48),
            'load_percent' => rand(65, 95),
        ]);


        ProductionLog::create([
            'message' => "🚀 {$line->name} запущена"
        ]);


        return back();
    }

    public function stop($id)
    {
        $line = ProductionLine::findOrFail($id);

        $line->update([
            'status' => 'Остановлена',
            'temperature' => 25,
            'load_percent' => 0,
        ]);


        ProductionLog::create([
            'message' => "🛑 {$line->name} остановлена"
        ]);


        return back();
    }
}
