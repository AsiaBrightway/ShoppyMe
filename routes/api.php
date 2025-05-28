<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\DeliveryChargesController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\TownshipController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Admin Login Route
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::post('/admin/userlogin', [AdminAuthController::class, 'userlogin']);

// User Sign Up
Route::post('/user/authphoneuser', [UserController::class, 'checkUserByPhone']);
Route::post('/user/authemailuser', [UserController::class, 'checkUserByEmail']);
// Route::post('/user/signin', [AdminAuthController::class, 'signin']);

// Admin Refresh Token Route
Route::post('/admin/refresh-token', [AdminAuthController::class, 'refreshToken']);
Route::apiResource('stores', StoreController::class);
Route::apiResource('admins', AdminController::class);

//Slider Route
Route::get('/sliders', [SliderController::class, 'index']);
Route::get('/subCategories', [SubCategoryController::class, 'index']);

//All Pages Access For Users
// Route::get('/colors', [ColorController::class, 'index']); // Get Active floors
// Protect all routes with authentication
Route::middleware('auth:sanctum')->group(function () {


    Route::apiResource('colors', ColorController::class);
    Route::apiResource('sizes', SizeController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('subCategories', SubCategoryController::class)->only(['destroy', 'store', 'show', 'update']);;
    Route::apiResource('products', ProductController::class);
    Route::apiResource('sliders', ColorController::class)->only(['destroy', 'store', 'show', 'update']);
    // Route::apiResource('sliders', SliderController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('townships', TownshipController::class);
    Route::apiResource('deliverycharges', DeliveryChargesController::class);

    // Logout Route
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
});
