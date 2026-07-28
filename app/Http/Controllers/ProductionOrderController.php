<?php

namespace App\Http\Controllers;

use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{

    public function index()
    {
        return Inertia::render('ProductionOrders/Index', [

            'orders' => ProductionOrder::latest()->get()

        ]);
    }



    public function show($id)
    {

        return Inertia::render(
            'ProductionOrders/Show',
            [
                'order' => ProductionOrder::findOrFail($id)
            ]
        );

    }



    public function create()
    {
        return Inertia::render(
            'ProductionOrders/Create'
        );
    }



    public function store(Request $request)
    {

        $validated = $request->validate([

            'customer_name' => 'required|string',

            'material' => 'required|string',

            'quantity' => 'required|integer'

        ]);



        ProductionOrder::create([

            'customer_name' => $validated['customer_name'],

            'material' => $validated['material'],

            'quantity' => $validated['quantity'],

            'status' => 'Новый'

        ]);



        return redirect('/dashboard');

    }





    public function edit($id)
    {

        return Inertia::render(
            'ProductionOrders/Edit',
            [
                'order' => ProductionOrder::findOrFail($id)
            ]
        );

    }





    public function update(Request $request, $id)
    {

        $order = ProductionOrder::findOrFail($id);


        $validated = $request->validate([

            'customer_name' => 'required|string',

            'material' => 'required|string',

            'quantity' => 'required|integer'

        ]);



        $order->update($validated);



        return redirect('/production-orders');

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


            'orders' => ProductionOrder::latest()
                ->take(5)
                ->get(),


            'lines' => ProductionLine::all(),

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





    public function destroy($id)
    {

        ProductionOrder::findOrFail($id)->delete();


        return back();

    }

}
