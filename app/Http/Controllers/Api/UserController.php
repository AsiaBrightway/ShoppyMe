<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\AdminResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\UserRefreshToken;
use Illuminate\Support\Str; // For generating refresh tokens
use Illuminate\Validation\ValidationException;
class UserController extends Controller
{
       /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get();
        if ($users->count() > 0) {
            return AdminResource::collection($users);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }





    public function Login(Request $request)
    {

    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new AdminResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
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
        $user->update([
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'store_id' => $request->store_id,
            'is_active' => $request->is_active
        ]);
        return response()->json([
            'message' => 'Admin Account Updated Successfully',
            'data' => new AdminResource($user)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'Admin Account Deleted Successfully',
        ], 200);
    }

    public function checkUserByPhone(Request $request)
    {
        try
        {
            // Validate the request input
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|max:191'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->messages()
                ], 422);
            }

            // Find user by email and phone number
            $user = User::where('phone_number', $request->phone_number)->first();

            if (!$user ) {
                            $user = User::create([
                                'email' => 'email',
                                'phone_number' => $request->phone_number,
                                'password' => "password",
                                'email_verified_at' => now(),
                            ]);

                            // Generate access token (valid for 1 hour)
                            $accessToken = $user->createToken('access_token', ['*'])->plainTextToken;
                            logger($user);  
                            // Generate refresh token (valid for 7 days)
                            $token = Str::uuid()->toString();
                            $refreshToken = Str::random(60);
                            UserRefreshToken::create([
                                'user_id' => $user->user_id,
                                'token' => hash('sha256', $token), // Store the hashed token
                                'expires_at' => now()->addDays(7),
                            ]);

                            return response()->json([
                                'message' => 'User Account Created Successfully',
                                'data' => [
                                    'status'=>true,
                                    'user_id'=>$user->user_id,
                                    'email' => $user->email,
                                    'phone_number' => $user->phone_number,
                                    'created_at' => $user->created_at,
                                    'access_token' => $accessToken,
                                    'refresh_token' =>  hash('sha256', $token)
                                ]
                            ], 201); // Use 201 for successful resource creation
            }
            else{
                          $user = User::where('phone_number', $request->number)->first();
        
        // Check if admin exists and the password matches
        if (!$user) {
            throw ValidationException::withMessages([
                'message' => ['Invalid credentials.'],
            ]);
        }

        // Delete old tokens for this admin
        $user->tokens()->delete();

        // Generate access token (valid for 1 hour)
        $accessToken = $user->createToken('access_token', ['*'])->plainTextToken;

        // Generate refresh token (valid for 7 days)
        $token = Str::uuid()->toString();
        $refreshToken = Str::random(60);
        UserRefreshToken::create([
            'user_id' => $user->user_id,
            'token' => hash('sha256', $token), // Store the hashed token
            'expires_at' => now()->addDays(7),
        ]);

        // Return response with tokens and admin details
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $accessToken,
            'refresh_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600, // Access token expiration (1 hour)
            'admin' => [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'phone_number' => $user->phone_number
            ],
        ], 200);
            }


        } catch(\Throwable $th){
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ], 500);
        }

    }
 

    public function checkUserByEmail(Request $request)
    {
        try
        {
            // Validate the request input
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|max:191'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->messages()
                ], 422);
            }

            // Find user by email and phone number
            $user = User::where('email', $request->email)->first();

            if (!$user ) {
                            $user = User::create([
                            'email' => $request->email,
                            'phone_number' => "phone",
                            'password' => "password",
                            'email_verified_at' => now(),
                        ]);

                        // Generate access token (valid for 1 hour)
                        $accessToken = $user->createToken('access_token', ['*'])->plainTextToken;

                        // Generate refresh token (valid for 7 days)
                        $token = Str::uuid()->toString();
                        $refreshToken = Str::random(60);
                        UserRefreshToken::create([
                            'user_id' => $user->user_id,
                            'token' => hash('sha256', $token), // Store the hashed token
                            'expires_at' => now()->addDays(7),
                        ]);

                        return response()->json([
                            'message' => 'User Account Created Successfully',
                            'data' => [
                                'status'=>true,
                                'email' => $user->email,
                                'phone_number' => $user->phone_number,
                                'created_at' => $user->created_at,
                                'access_token' => $accessToken,
                                'refresh_token' =>  hash('sha256', $token)

                            ]
                        ], 201); // Use 201 for successful resource creation
            }
            else{
                          $user = User::where('email', $request->email)->first();
        
                            // Check if admin exists and the password matches
                            if (!$user) {
                                throw ValidationException::withMessages([
                                    'message' => ['Invalid credentials.'],
                                ]);
                            }

                            // Delete old tokens for this admin
                            $user->tokens()->delete();

                            // Generate access token (valid for 1 hour)
                            $accessToken = $user->createToken('access_token', ['*'])->plainTextToken;

                            // Generate refresh token (valid for 7 days)
                            $token = Str::uuid()->toString();
                            $refreshToken = Str::random(60);
                            UserRefreshToken::create([
                                'user_id' => $user->user_id,
                                'token' => hash('sha256', $token), // Store the hashed token
                                'expires_at' => now()->addDays(7),
                            ]);

                            // Return response with tokens and admin details
                            return response()->json([
                                'message' => 'Login successful',
                                'access_token' => $accessToken,
                                'refresh_token' => $token,
                                'token_type' => 'Bearer',
                                'expires_in' => 3600, // Access token expiration (1 hour)
                                'admin' => [
                                    'user_id' => $user->user_id,
                                    'email' => $user->email,
                                    'phone_number' => $user->phone_number
                                ],
                            ], 200);
            }


        } catch(\Throwable $th){
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ], 500);
        }

    }



    public function userrefreshToken(Request $request)
    {
        // Validate refresh token
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        // Hash the provided refresh token and look it up in the database
        $hashedToken = hash('sha256', $request->refresh_token);
        $refreshToken = UserRefreshToken::where('token', $hashedToken)->first();

        // Check if the refresh token is valid and not expired
        if (!$refreshToken || $refreshToken->expires_at < now()) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        // Get the admin associated with the refresh token
        $admin = $refreshToken->admin;

        // Generate a new access token
        $accessToken = $admin->createToken('access_token', ['*'])->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600, // 1 hour
        ]);
    }
}
