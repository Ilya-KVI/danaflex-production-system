<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function index()
    {
        return Inertia::render('ProductionOrders/Index', [
            'orders' => ProductionOrder::all()
        ]);
    }

    public function dashboard()
    {
        return Inertia::render('Dashboard', [

            'statistics' => [

                'total' => ProductionOrder::count(),

                'inProduction' => ProductionOrder::where(
                    'status',
                    'В производстве'
                )->count(),

                'completed' => ProductionOrder::where(
                    'status',
                    'Готово'
                )->count(),

            ],

            'orders' => ProductionOrder::latest()->take(5)->get()

        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = ProductionOrder::findOrFail($id);


        $order->update([
            'status' => $request->status
        ]);


        return back();
    }
}
