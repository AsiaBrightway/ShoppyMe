<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::get();
        if ($stores->count() > 0) {
            return StoreResource::collection($stores);
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
            'store_name' => 'required|string|max:191',
            'owner_name' => 'required|string|max:191',
            'address' => 'required',
            'phone_number' => 'required|numeric|digits_between:7,11',
            'email' => 'nullable|email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $store = Store::create([
            'store_name' => $request->store_name,
            'owner_name' => $request->owner_name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'email' => $request->email
        ]);

        return response()->json([
            'message' => 'Store Created Successfully',
            'data' => $store
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        return new StoreResource($store);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|string|max:191',
            'owner_name' => 'required|string|max:191',
            'address' => 'required',
            'phone_number' => 'required|numeric|digits_between:7,11',
            'email' => 'nullable|email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $store->update([
            'store_name' => $request->store_name,
            'owner_name' => $request->owner_name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'email' => $request->email
        ]);
        return response()->json([
            'message' => 'Store Updated Successfully',
            'data' => new StoreResource($store)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();
        return response()->json([
            'message' => 'Store Deleted Successfully',
        ], 200);
    }
}
