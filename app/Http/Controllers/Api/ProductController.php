<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with([
            'subCategory.category', // Load category via subCategory
            'brand',
            'images',
            'prices.size',
            'prices.color'
        ])->get();

        if ($products->isNotEmpty()) {
            return ProductResource::collection($products);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $product_images = $request->input('productImages', []); // Default to an empty array
            $product_prices = $request->input('productPrices', []); // Default to an empty array

            $validator = Validator::make($request->all(), [
                'sub_category_id' => 'required',
                'brand_id' => 'required',
                'product_name' => 'required|string|max:191',
                'product_description' => 'required|string|max:191',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'All fields are mandatory',
                    'error' => $validator->messages()
                ], 422);
            }

            $product = Product::create([
                'sub_category_id' => $request->sub_category_id,
                'brand_id' => $request->brand_id,
                'product_name' => $request->product_name,
                'product_description' => $request->product_description,
            ]);

            $product_id = $product->product_id;

            // Log product creation success
            logger('Product created successfully:', [
                'id' => $product->product_id,
                'name' => $product->product_name,
                'sub_category_id' => $product->sub_category_id,
                'brand_id' => $product->brand_id,
            ]);

            if (!empty($product_images)) {
                foreach ($product_images as $product_image) {
                    if (!empty($product_image['image'])) {
                        // Validate base64 image string
                        $imageValidator = Validator::make($product_image, [
                            'image' => 'nullable|string'
                        ]);

                        if ($imageValidator->fails()) {
                            return response()->json([
                                'message' => 'Invalid image format',
                                'error' => $imageValidator->messages()
                            ], 422);
                        }

                        // Decode and save image
                        $decodedImage = base64_decode($product_image['image']);
                        if ($decodedImage === false) {
                            return response()->json(['error' => 'Invalid image data'], 422);
                        }

                        // Generate unique filename
                        $fileName = uniqid() . '_product_image.png';
                        file_put_contents(public_path('Images/') . $fileName, $decodedImage);

                        // Save image record
                        ProductImage::create([
                            'product_id' => $product_id,
                            'image' => $fileName
                        ]);
                    }
                }
            }

            if (!empty($product_prices)) {
                foreach ($product_prices as $product_price) {
                    if (!empty($product_price)) {
                        // Validate price data
                        $productPriceValidator = Validator::make($product_price, [
                            'size_id' => 'required',
                            'color_id' => 'required',
                            'price' => 'required|numeric',
                            'stock_qty' => 'required|integer',
                            'return_points' => 'required|numeric',
                            'is_promotion' => 'required|boolean',
                            'promotion_price' => 'nullable|numeric'
                        ]);

                        if ($productPriceValidator->fails()) {
                            return response()->json([
                                'message' => 'Invalid product price data',
                                'error' => $productPriceValidator->messages()
                            ], 422);
                        }

                        ProductPrice::create([
                            'product_id' => $product_id,
                            'size_id' => $product_price['size_id'],
                            'color_id' => $product_price['color_id'],
                            'price' => $product_price['price'],
                            'stock_qty' => $product_price['stock_qty'],
                            'return_points' => $product_price['return_points'],
                            'is_promotion' => $product_price['is_promotion'],
                            'promotion_price' => $product_price['promotion_price']
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Product Created Successfully',
                'data' => $product
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load([
            'subCategory.category', // Load category via subCategory
            'brand',
            'images',
            'prices.size',
            'prices.color'
        ]);

        return new ProductResource($product);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            DB::beginTransaction();

            $product_id = $product->product_id;

            $product_images = $request->input('productImages', []); // Default to an empty array
            $product_prices = $request->input('productPrices', []); // Default to an empty array
            // $product_images = json_decode($request->input('productImages'), true);
            // $product_prices = json_decode($request->input('productPrices'), true);

            // Validate product data
            $validator = Validator::make($request->all(), [
                'sub_category_id' => 'required|integer',
                'brand_id' => 'required|integer',
                'product_name' => 'required|string|max:191',
                'product_description' => 'required|string|max:191',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'All fields are mandatory',
                    'error' => $validator->messages()
                ], 422);
            }

            // Update product details
            $product->update([
                'sub_category_id' => $request->sub_category_id,
                'brand_id' => $request->brand_id,
                'product_name' => $request->product_name,
                'product_description' => $request->product_description,
            ]);

            // Handle product images
            if (!empty($product_images)) {
                foreach ($product_images as $product_image) {
                    if (!empty($product_image['image'])) {
                        // Validate image
                        $imageValidator = Validator::make($product_image, [
                            'image' => 'nullable|string', // Expecting base64 string
                        ]);

                        if ($imageValidator->fails()) {
                            return response()->json([
                                'message' => 'Invalid image format',
                                'error' => $imageValidator->messages()
                            ], 422);
                        }

                        // Decode and save image
                        $decodedImage = base64_decode($product_image['image']);
                        if ($decodedImage === false) {
                            return response()->json(['error' => 'Invalid image data'], 422);
                        }

                        // Generate file name
                        $fileName = uniqid() . '_product_image.png';
                        $imagePath = public_path('Images/') . $fileName;

                        // Save image to disk
                        file_put_contents($imagePath, $decodedImage);

                        // Delete old image (if exists)
                        $existingImage = ProductImage::where('product_id', $product_id)->first();
                        if ($existingImage && file_exists(public_path('Images/' . $existingImage->image))) {
                            unlink(public_path('Images/' . $existingImage->image));
                            $existingImage->delete();
                        }

                        // Save new image record
                        ProductImage::create([
                            'product_id' => $product_id,
                            'image' => $fileName
                        ]);
                    }
                }
            }

            // Handle product prices
            if (!empty($product_prices)) {
                foreach ($product_prices as $product_price) {
                    if (!empty($product_price)) {
                        // Validate price data
                        $productPriceValidator = Validator::make($product_price, [
                            'size_id' => 'required|integer',
                            'color_id' => 'required|integer',
                            'price' => 'required|numeric|min:0',
                            'stock_qty' => 'required|integer|min:0',
                            'return_points' => 'required|numeric|min:0',
                            'is_promotion' => 'required|boolean',
                            'promotion_price' => 'nullable|numeric|min:0'
                        ]);

                        if ($productPriceValidator->fails()) {
                            return response()->json([
                                'message' => 'Invalid product price data',
                                'error' => $productPriceValidator->messages()
                            ], 422);
                        }

                        // Create or update product price
                        ProductPrice::updateOrCreate(
                            [
                                'product_id' => $product_id,
                                'size_id' => $product_price['size_id'],
                                'color_id' => $product_price['color_id'],
                            ],
                            [
                                'price' => $product_price['price'],
                                'stock_qty' => $product_price['stock_qty'],
                                'return_points' => $product_price['return_points'],
                                'is_promotion' => $product_price['is_promotion'],
                                'promotion_price' => $product_price['promotion_price'] ?? null
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Product Updated Successfully',
                'data' => new ProductResource($product)
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            logger()->error('Product update error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            $product_id = $product->product_id;

            // Delete associated images
            $productImages = ProductImage::where('product_id', $product_id)->get();
            foreach ($productImages as $image) {
                $imagePath = public_path('Images/' . $image->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }

            // Delete associated product prices
            ProductPrice::where('product_id', $product_id)->delete();

            // Delete the product
            $product->delete();

            DB::commit();

            return response()->json([
                'message' => 'Product Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());

            return response()->json([
                'message' => 'Error deleting product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
