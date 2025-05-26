<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = Slider::get();
        if ($sliders->count() > 0) {
            return SliderResource::collection($sliders);
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
            'slider_name' => 'required|string|max:191',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = uniqid() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('Images'), $fileName);
        }

        $slider = Slider::create([
            'slider_name' => $request->slider_name,
            'image' => $fileName
        ]);

        return response()->json([
            'message' => 'Slider Created Successfully',
            'data' => $slider
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider)
    {
        return new SliderResource($slider);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slider $slider)
    {
        $validator = Validator::make($request->all(), [
            'slider_name' => 'required|string|max:191',
            'image' => 'nullable|string', // Expecting a Base64 encoded string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fileName = $slider->image;

        if ($request->filled('image')) {
            $decodedImage = base64_decode($request->image);
            if ($decodedImage === false) {
                return response()->json(['error' => 'Invalid image data'], 422);
            }

            if (!empty($slider->image)) {
                $oldImagePath = public_path('Images/' . $slider->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $fileName = uniqid() . '_slider_image.png';
            file_put_contents(public_path('Images/') . $fileName, $decodedImage);
        }

        $slider->update([
            'slider_name' => $request->slider_name,
            'image' => $fileName, // Save the new image file name
        ]);

        return response()->json([
            'message' => 'Slider Updated Successfully',
            'data' => new SliderResource($slider),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        try {
            DB::beginTransaction();

            $slider_id = $slider->slider_id;

            // Delete associated images
            $imagePath = public_path('Images/' . $slider->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete associated product prices
            Slider::where('slider_id', $slider_id)->delete();

            // Delete the product
            $slider->delete();

            DB::commit();

            return response()->json([
                'message' => 'Slider Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());

            return response()->json([
                'message' => 'Error deleting slider',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
