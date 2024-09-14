<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthRequestLogin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(AuthRequestLogin $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user){
            return response()->json([
                'message' => 'User not found.',
            ], 400);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The password entered is incorrect.',
            ], 400);
        }

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password']
        ];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'Login successfully.',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60  // Duração do token em segundos
        ]);

        return response()->json($user, 200);
    }
}
