<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = Color::get();
        if ($colors->count() > 0) {
            return ColorResource::collection($colors);
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
            'color_name' => 'required|string|max:191',
            'color_code' => 'required|string|max:191',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $color = Color::create([
            'color_name' => $request->color_name,
            'color_code' => $request->color_code
        ]);

        return response()->json([
            'message' => 'Color Created Successfully',
            'data' => $color
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Color $color)
    {
        return new ColorResource($color);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Color $color)
    {
        $validator = Validator::make($request->all(), [
            'color_name' => 'required|string|max:191',
            'color_code' => 'required|string|max:191'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $color->update([
            'color_name' => $request->color_name,
            'color_code' => $request->color_code
        ]);
        return response()->json([
            'message' => 'Color Updated Successfully',
            'data' => new ColorResource($color)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $color->delete();
        return response()->json([
            'message' => 'Color Deleted Successfully',
        ], 200);
    }
}