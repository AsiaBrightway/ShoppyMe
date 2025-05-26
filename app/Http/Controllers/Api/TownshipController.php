<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TownshipResource;
use App\Models\Township;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TownshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $townships = Township::get();
        if ($townships->count() > 0) {
            return TownshipResource::collection($townships);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'city_id' => 'required|exists:cities,city_id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $township = Township::create([
            'name' => $request->name,
            'city_id' => $request->city_id
        ]);

        // Load the related city for response
        $township->load('city');

        return response()->json([
            'message' => 'City Created Successfully',
            'data' => [
                'township_id' => $township->township_id,
                'name' => $township->name,
                'created_at' => $township->created_at,
                'city' => $township->city,
            ]
        ], 201); // Use 201 for successful resource creation
    }

    /**
     * Display the specified resource.
     */
    public function show(Township $township)
    {
        return new TownshipResource($township);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Township $township)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'city_id' => 'required|exists:cities,city_id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $township->update([
            'name' => $request->name,
            'city_id' => $request->city_id
        ]);
        return response()->json([
            'message' => 'Township Updated Successfully',
            'data' => new TownshipResource($township)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Township $township)
    {
        $township->delete();
        return response()->json([
            'message' => 'Township Deleted Successfully',
        ], 200);
    }
}
