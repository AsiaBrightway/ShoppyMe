<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = Size::get();
        if ($sizes->count() > 0) {
            return SizeResource::collection($sizes);
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
            'size' => 'required|string|max:191'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $size = Size::create([
            'size' => $request->size
        ]);

        return response()->json([
            'message' => 'Size Created Successfully',
            'data' => $size
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Size $size)
    {
        return new SizeResource($size);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Size $size)
    {
        $validator = Validator::make($request->all(), [
            'size' => 'required|string|max:191'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $size->update([
            'size' => $request->size
        ]);
        return response()->json([
            'message' => 'Size Updated Successfully',
            'data' => new SizeResource($size)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        $size->delete();
        return response()->json([
            'message' => 'Size Deleted Successfully',
        ], 200);
    }
}