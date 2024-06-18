<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $orders = Order::all();
            return response()->json($orders, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $validatedData = $request->validate([
                'customer_id' => 'required|exists:users,id',
                'restaurant_id' => 'required|exists:restaurants,id',
                'order_type' => 'required|in:delivery,pickup',
                'total_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'order_status' => 'required|string',
            ]);

            $order = Order::create($validatedData);
            return response()->json($order, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $order = Order::findOrFail($id);
            return response()->json($order, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            $validatedData = $request->validate([
                'customer_id' => 'sometimes|required|exists:users,id',
                'restaurant_id' => 'sometimes|required|exists:restaurants,id',
                'order_type' => 'sometimes|required|in:delivery,pickup',
                'total_amount' => 'sometimes|required|numeric|min:0',
                'payment_method' => 'sometimes|required|string',
                'order_status' => 'sometimes|required|string',
            ]);

            $order = Order::findOrFail($id);
            $order->update($validatedData);
            return response()->json($order, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}