<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentications
 */
class AuthController extends Controller
{

    /**
     * @unauthenticated
     * 
     * Register A new user
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user
        ], 201);
    }

    /**
     * @unauthenticated
     * 
     * Login user and retrieve token
     */
    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * 
     * Logout user and revoke token
     */
    public function logout(Request $request)
    {
        $user = $request->user('api');

        $user->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
