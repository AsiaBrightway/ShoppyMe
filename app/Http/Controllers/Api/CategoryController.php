<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::get();
        if ($categories->count() > 0) {
            return CategoryResource::collection($categories);
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
            'category_name' => 'required|string|max:191',
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

        $category = Category::create([
            'category_name' => $request->category_name,
            'image' => $fileName
        ]);

        return response()->json([
            'message' => 'Category Created Successfully',
            'data' => $category
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:191',
            'image' => 'nullable|string', // Expecting a Base64 encoded string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fileName = $category->image;

        if ($request->filled('image')) {
            $decodedImage = base64_decode($request->image);
            if ($decodedImage === false) {
                return response()->json(['error' => 'Invalid image data'], 422);
            }

            if (!empty($category->image)) {
                $oldImagePath = public_path('Images/' . $category->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $fileName = uniqid() . '_category_image.png';
            file_put_contents(public_path('Images/') . $fileName, $decodedImage);
        }

        $category->update([
            'category_name' => $request->category_name,
            'image' => $fileName, // Save the new image file name
        ]);

        return response()->json([
            'message' => 'Category Updated Successfully',
            'data' => new CategoryResource($category),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            $category_id = $category->category_id;

            // Delete associated images
            $imagePath = public_path('Images/' . $category->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete associated product prices
            Category::where('category_id', $category_id)->delete();

            // Delete the product
            $category->delete();

            DB::commit();

            return response()->json([
                'message' => 'Category Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());

            return response()->json([
                'message' => 'Error deleting category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
