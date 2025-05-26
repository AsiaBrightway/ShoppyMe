<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryChargesResource;
use App\Models\DeliveryCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryCharges = DeliveryCharges::get();
        if ($deliveryCharges->count() > 0) {
            return DeliveryChargesResource::collection($deliveryCharges);
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
            'township_id' => 'required|exists:townships,township_id',
            'delivery_fee' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $deliveryCharges = DeliveryCharges::create([
            'township_id' => $request->township_id,
            'delivery_fee' => $request->delivery_fee
        ]);

        // Load the related city for response
        $deliveryCharges->load('township');

        return response()->json([
            'message' => 'Delivery Charges Created Successfully',
            'data' => [
                'delivery_charges_id' => $deliveryCharges->delivery_charges_id,
                'delivery_fee' => $deliveryCharges->delivery_fee,
                'created_at' => $deliveryCharges->created_at,
                'township' => $deliveryCharges->township,
            ]
        ], 201); // Use 201 for successful resource creation
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryCharges $deliveryCharges)
    {
        return new DeliveryChargesResource($deliveryCharges);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryCharges $deliveryCharges)
    {
        $validator = Validator::make($request->all(), [
            'township_id' => 'required|exists:townships,township_id',
            'delivery_fee' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $deliveryCharges->update([
            'township_id' => $request->township_id,
            'delivery_fee' => $request->delivery_fee
        ]);
        return response()->json([
            'message' => 'Delivery Charges Updated Successfully',
            'data' => new DeliveryChargesResource($deliveryCharges)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryCharges $deliveryCharges)
    {
        $deliveryCharges->delete();
        return response()->json([
            'message' => 'Delivery Charges Deleted Successfully',
        ], 200);
    }
}
