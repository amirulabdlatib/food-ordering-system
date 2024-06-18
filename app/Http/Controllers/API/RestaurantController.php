<?php

namespace App\Http\Controllers\API;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $restaurants = Restaurant::all();
            return response()->json($restaurants, 200);
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
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'manager_id' => 'nullable|exists:users,id',
            ]);

            $restaurant = Restaurant::create($validatedData);
            return response()->json($restaurant, 201);
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
            $restaurant = Restaurant::findOrFail($id);
            return response()->json($restaurant, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not found'], 404);
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
                'name' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|required|string|max:255',
                'phone_number' => 'sometimes|required|string|max:20',
                'description' => 'nullable|string',
                'category_id' => 'sometimes|required|exists:categories,id',
                'manager_id' => 'nullable|exists:users,id',
            ]);

            $restaurant = Restaurant::findOrFail($id);
            $restaurant->update($validatedData);
            return response()->json($restaurant, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not found'], 404);
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
            $restaurant = Restaurant::findOrFail($id);
            $restaurant->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}