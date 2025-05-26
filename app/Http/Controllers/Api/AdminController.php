<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::get();
        if ($admins->count() > 0) {
            return AdminResource::collection($admins);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:191',
            'password' => 'required|string|min:6',
            'store_id' => 'required|exists:stores,store_id', // Ensure store_id exists in stores table
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->messages()
            ], 422);
        }

        // Create the admin
        $admin = Admin::create([
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'store_id' => $request->store_id,
            'is_active' => $request->is_active
        ]);

        // Load the related store for response
        $admin->load('store');

        return response()->json([
            'message' => 'Admin Account Created Successfully',
            'data' => [
                'admin_id' => $admin->admin_id,
                'user_name' => $admin->user_name,
                'is_active' => $admin->is_active,
                'created_at' => $admin->created_at,
                'store' => $admin->store,
                // 'store' => [
                //     'store_id' => $admin->store->store_id,
                //     'store_name' => $admin->store->store_name
                // ],
            ]
        ], 201); // Use 201 for successful resource creation
    }


    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return new AdminResource($admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:191',
            'password' => 'required|min:6',
            'store_id' => 'required',
            'is_active' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandetory',
                'error' => $validator->messages()
            ], 422);
        }
        $admin->update([
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'store_id' => $request->store_id,
            'is_active' => $request->is_active
        ]);
        return response()->json([
            'message' => 'Admin Account Updated Successfully',
            'data' => new AdminResource($admin)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();
        return response()->json([
            'message' => 'Admin Account Deleted Successfully',
        ], 200);
    }
}
