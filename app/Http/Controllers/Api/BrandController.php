<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::get();
        if ($brands->count() > 0) {
            return BrandResource::collection($brands);
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
            'brand_name' => 'required|string|max:191',
            'brand_description' => 'string|max:191',
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

        $brand = Brand::create([
            'brand_name' => $request->brand_name,
            'brand_description' => $request->brand_description,
            'image' => $fileName
        ]);

        return response()->json([
            'message' => 'Brand Created Successfully',
            'data' => $brand
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        if ($request->filled('image')) {
            // Attempt to decode the base64 string
            $decodedImage = base64_decode($request->image);
            if ($decodedImage === false) {
                return response()->json(['error' => 'Invalid image data'], 422);
            }
            // Optionally, perform further validation on $decodedImage (mime type, size, etc.)
            
            // Generate a unique file name
            $fileName = uniqid() . '_brand_image.png'; // or derive extension after validating type
            
            // Save the image to a path
            file_put_contents(public_path('Images') . '/' . $fileName, $decodedImage);
        } else {
            $fileName = $brand->image; // keep the old image if not provided
        }

        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|string|max:191',
            'brand_description' => 'string|max:191',
        
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages()
            ], 422);
        }

        $brand->update([
            'brand_name' => $request->brand_name,
            'brand_description' => $request->brand_description,
            'image' => $fileName,
        ]);

        return response()->json([
            'message' => 'Brand Updated Successfully',
            'data' => new BrandResource($brand)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->json([
            'message' => 'Brand Deleted Successfully',
        ], 200);
    }
}
