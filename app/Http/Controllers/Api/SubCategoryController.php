<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;


class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subCategories = SubCategory::get();
        if ($subCategories->count() > 0) {
            return SubCategoryResource::collection($subCategories);
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
            'category_id' => 'required',
            'sub_category_name' => 'required|string|max:191',
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

        $subCategory = SubCategory::create([
            'category_id' => $request->category_id,
            'sub_category_name' => $request->sub_category_name,
            'image' => $fileName
        ]);
        $subCategory->load('category');

        return response()->json([
            'message' => 'Sub Category Created Successfully',
            'data'   => $subCategory
        ], 201); // Use 201 for successful resource creation
    }


    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory)
    {
        return new SubCategoryResource($subCategory);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, SubCategory $subCategory)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'sub_category_name' => 'required|string|max:191',
            'image' => 'nullable|string', // Expecting Base64 string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fileName = $subCategory->image;

        if ($request->filled('image')) {
            // Decode Base64
            $decodedImage = base64_decode($request->image);
            if ($decodedImage === false) {
                return response()->json(['error' => 'Invalid image data'], 422);
            }

            if (!empty($subCategory->image)) {
                $oldImagePath = public_path('Images/' . $subCategory->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $fileName = uniqid() . '_subcategory_image.png';
            file_put_contents(public_path('Images/') . $fileName, $decodedImage);
        }

        // Update sub-category details
        $subCategory->update([
            'category_id' => $request->category_id,
            'sub_category_name' => $request->sub_category_name,
            'image' => $fileName, // Save the new image file name
        ]);

        return response()->json([
            'message' => 'SubCategory Updated Successfully',
            'data' => new SubCategoryResource($subCategory),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory)
    {
        try {
            DB::beginTransaction();

            $sub_category_id = $subCategory->sub_category_id;

            // Delete associated images
            $imagePath = public_path('Images/' . $subCategory->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete associated product prices
            SubCategory::where('sub_category_id', $sub_category_id)->delete();

            // Delete the product
            $subCategory->delete();

            DB::commit();

            return response()->json([
                'message' => 'Sub Category Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());

            return response()->json([
                'message' => 'Error deleting sub category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
