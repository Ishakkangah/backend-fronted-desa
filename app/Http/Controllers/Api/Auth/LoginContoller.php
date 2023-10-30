<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginContoller extends Controller
{
    // Index
    public function index(Request $request)
    {
        // Set validasi
        $validator          = Validator::make($request->all(), [
            'email'         => 'required|email',
            'password'      => 'required'
        ]);

        // Response error validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Get "email" dan "password" dari input
        $credentials    = $request->only('email', 'password');

        // Check jika email dan password tidak sesuai
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            // response login failed
            return response()->json([
                'success'       => false,
                'message'       => 'Email & Password Is Incorrect'
            ], 400);
        }

        //response login "success" dengan generate "Token"
        return response()->json([
            'success'       => true,
            'user'          => auth()->guard('api')->user()->only(['name', 'email']),
            'permissions'   => auth()->guard('api')->user()->getPermissionArray(),
            'token'         => $token
        ], 200);
    }

    // Logout
    public function logout()
    {
        //remove "token" JWT
        JWTAuth::invalidate(JWTAuth::getToken());

        //response "success" logout
        return response()->json([
            'success' => true,
        ], 200);
    }
}
