<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Models\RefreshToken; // Assuming this is the model for storing refresh tokens
use App\Models\UserRefreshToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // For generating refresh tokens

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        logger($request);
        // Validate the incoming request
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find the admin by username
        $admin = Admin::where('user_name', $request->user_name)->first();

        // Check if admin exists and the password matches
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'message' => ['Invalid credentials.'],
            ]);
        }

        // Delete old tokens for this admin
        $admin->tokens()->delete();

        // Generate access token (valid for 1 hour)
        $accessToken = $admin->createToken('access_token', ['*'])->plainTextToken;

        // Generate refresh token (valid for 7 days)
        $refreshToken = Str::random(60);
        RefreshToken::create([
            'admin_id' => $admin->admin_id,
            'token' => hash('sha256', $refreshToken), // Store the hashed token
            'expires_at' => now()->addDays(7),
        ]);

        // Return response with tokens and admin details
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $accessToken,
            'refresh_token' => hash('sha256', $refreshToken),
            'token_type' => 'Bearer',
            'expires_in' => 3600, // Access token expiration (1 hour)
            'admin' => [
                'admin_id' => $admin->admin_id,
                'user_name' => $admin->user_name,
                'store_id' => $admin->store_id,
                'is_active' => $admin->is_active,
            ],
        ], 200);
    }

    public function userlogin(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find the admin by username
        $user = User::where('email', $request->email)->first();
        
        // Check if admin exists and the password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
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

    public function refreshToken(Request $request)
    {
        // Validate refresh token
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        // Hash the provided refresh token and look it up in the database
        $hashedToken = hash('sha256', $request->refresh_token);
        $refreshToken = RefreshToken::where('token', $hashedToken)->first();

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

    
 

    public function logout(Request $request)
    {
        // Revoke all tokens for the current user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
}
