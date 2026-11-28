<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user=User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'location' => $request->location
        ]);

        $token=$user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Account Registered Successfully',
            'token' => $token
        ], 200);
    }

    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->only('phone','password')))
        {
            return response()->json([
                'message' => 'Invalid phone number or password!'
            ], 401);
        }

        $user=User::where('phone',$request->phone)->firstOrFail();
        $token=$user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Successful'
        ], 200);
    }
}
