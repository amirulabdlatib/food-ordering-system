<?php

namespace App\Http\Controllers\API;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $sales = Sale::all();
            return response()->json($sales, 200);
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
                'restaurant_id' => 'required|exists:restaurants,id',
                'total_sales' => 'required|numeric',
            ]);

            $sale = Sale::create($validatedData);
            return response()->json($sale, 201);
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
            $sale = Sale::findOrFail($id);
            return response()->json($sale, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Sale not found'], 404);
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
                'restaurant_id' => 'sometimes|required|exists:restaurants,id',
                'total_sales' => 'sometimes|required|numeric',
            ]);

            $sale = Sale::findOrFail($id);
            $sale->update($validatedData);
            return response()->json($sale, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Sale not found'], 404);
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
            $sale = Sale::findOrFail($id);
            $sale->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Sale not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
        
    }
}