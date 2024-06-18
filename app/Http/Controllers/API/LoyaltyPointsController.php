<?php

namespace App\Http\Controllers\API;

use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoyaltyPointsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $loyaltyPoints = LoyaltyPoint::all();
            return response()->json($loyaltyPoints, 200);
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
                'points_earned' => 'required|integer|min:0',
                'points_redeemed' => 'required|integer|min:0',
            ]);

            $loyaltyPoints = LoyaltyPoint::create($validatedData);
            return response()->json($loyaltyPoints, 201);
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
            $loyaltyPoints = LoyaltyPoint::findOrFail($id);
            return response()->json($loyaltyPoints, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Loyalty points not found'], 404);
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
                'points_earned' => 'sometimes|required|integer|min:0',
                'points_redeemed' => 'sometimes|required|integer|min:0',
            ]);

            $loyaltyPoints = LoyaltyPoint::findOrFail($id);
            $loyaltyPoints->update($validatedData);
            return response()->json($loyaltyPoints, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Loyalty points not found'], 404);
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
            $loyaltyPoints = LoyaltyPoint::findOrFail($id);
            $loyaltyPoints->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Loyalty points not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}