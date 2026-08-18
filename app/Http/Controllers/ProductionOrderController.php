<?php

namespace App\Http\Controllers;

use App\Models\ProductionLine;
use App\Models\ProductionLog;
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

        $lineName = null;

        if ($order->production_line_id) {
            $lineName = ProductionLine::find($order->production_line_id)?->name;
        }

        $message = "Создан заказ #{$order->id}: "
            . "{$order->customer_name}, "
            . "{$order->material}, "
            . "{$order->quantity} ед.";

        if ($lineName) {
            $message .= " Линия: {$lineName}.";
        } else {
            $message .= " Линия не назначена.";
        }

        ProductionLog::create([
            'message' => $message,
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

        $changes = [];

        if ($order->customer_name !== $validated['customer_name']) {
            $changes[] = "заказчик: «{$order->customer_name}» → «{$validated['customer_name']}»";
        }

        if ($order->material !== $validated['material']) {
            $changes[] = "материал: «{$order->material}» → «{$validated['material']}»";
        }

        if ((int) $order->quantity !== (int) $validated['quantity']) {
            $changes[] = "объём: {$order->quantity} → {$validated['quantity']} ед.";
        }

        $newLineId = $validated['production_line_id'] ?? null;

        if ((string) ($order->production_line_id ?? '') !== (string) ($newLineId ?? '')) {
            $oldLineName = $order->production_line_id
                ? ProductionLine::find($order->production_line_id)?->name
                : null;

            $newLineName = $newLineId
                ? ProductionLine::find($newLineId)?->name
                : null;

            $changes[] = "линия: "
                . ($oldLineName ?? 'не назначена')
                . " → "
                . ($newLineName ?? 'не назначена');
        }

        $order->update($validated);

        if ($changes) {
            ProductionLog::create([
                'message' => "Обновлён заказ #{$order->id}: "
                    . implode('; ', $changes)
                    . '.',
            ]);
        }

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

            'logs' => ProductionLog::latest()
                ->take(10)
                ->get(),
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

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return back();
        }

        $order->update([
            'status' => $newStatus,
        ]);

        ProductionLog::create([
            'message' => "Заказ #{$order->id}: "
                . "статус «{$oldStatus}» → «{$newStatus}».",
        ]);

        return back();
    }


    public function destroy($id)
    {
        $order = ProductionOrder::findOrFail($id);

        ProductionLog::create([
            'message' => "Удалён заказ #{$order->id}: "
                . "{$order->customer_name}, {$order->material}.",
        ]);

        $order->delete();

        return redirect()->route(
            'production-orders.index'
        );
    }
}
