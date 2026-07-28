<?php

namespace App\Http\Controllers;

use App\Models\ProductionLog;
use Inertia\Inertia;

class ProductionLogController extends Controller
{

    public function index()
    {

        return Inertia::render(
            'ProductionLogs/Index',
            [

                'logs' => ProductionLog::latest()->get()

            ]
        );

    }

}
