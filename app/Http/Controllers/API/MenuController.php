<?php

namespace App\Http\Controllers\API;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $menus = Menu::all();
            return response()->json($menus, 200);
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
            ]);

            $menu = Menu::create($validatedData);
            return response()->json($menu, 201);
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
            $menu = Menu::findOrFail($id);
            return response()->json($menu, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not found'], 404);
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
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
            ]);

            $menu = Menu::findOrFail($id);
            $menu->update($validatedData);
            return response()->json($menu, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not found'], 404);
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
            $menu = Menu::findOrFail($id);
            $menu->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}