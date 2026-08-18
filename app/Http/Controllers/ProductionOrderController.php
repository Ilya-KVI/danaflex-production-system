<?php

namespace App\Http\Controllers;

use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProductionOrderController extends Controller
{
    public function index()
    {
        return Inertia::render('ProductionOrders/Index', [
            'orders' => ProductionOrder::latest()->get(),
        ]);
    }


    public function show($id)
    {
        return Inertia::render('ProductionOrders/Show', [
            'order' => ProductionOrder::with('productionLine')
                ->findOrFail($id),
        ]);
    }


    public function create()
    {
        return Inertia::render('ProductionOrders/Create', [
            'lines' => ProductionLine::orderBy('name')->get(),
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'material' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'production_line_id' => [
                'nullable',
                'integer',
                'exists:production_lines,id',
            ],
        ]);

        $order = ProductionOrder::create([
            ...$validated,
            'status' => 'Новый',
        ]);

        return redirect()->route(
            'production-orders.show',
            $order->id
        );
    }


    public function edit($id)
    {
        return Inertia::render('ProductionOrders/Edit', [
            'order' => ProductionOrder::findOrFail($id),

            'lines' => ProductionLine::orderBy('name')->get(),
        ]);
    }


    public function update(Request $request, $id)
    {
        $order = ProductionOrder::findOrFail($id);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'material' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'production_line_id' => [
                'nullable',
                'integer',
                'exists:production_lines,id',
            ],
        ]);

        $order->update($validated);

        return redirect()->route(
            'production-orders.show',
            $order->id
        );
    }


    public function dashboard()
    {
        $queuedCount = ProductionOrder::where(
            'status',
            'Новый'
        )->count();

        $productionVolume = ProductionOrder::where(
            'status',
            'В производстве'
        )->sum('quantity');

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

                'queued' => $queuedCount,

                'productionVolume' => $productionVolume,
            ],

            'orders' => ProductionOrder::latest()
                ->take(5)
                ->get(),

            'workQueue' => ProductionOrder::whereIn(
                'status',
                [
                    'Новый',
                    'В производстве',
                ]
            )
                ->latest()
                ->take(10)
                ->get(),

            'lines' => ProductionLine::orderBy('name')->get(),
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'Новый',
                    'В производстве',
                    'Готово',
                ]),
            ],
        ]);

        $order = ProductionOrder::findOrFail($id);

        $order->update([
            'status' => $validated['status'],
        ]);

        return back();
    }


    public function destroy($id)
    {
        ProductionOrder::findOrFail($id)->delete();

        return redirect()->route(
            'production-orders.index'
        );
    }
}
