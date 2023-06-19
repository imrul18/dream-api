<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json([
            'message' => 'User not found',
            'status' => 203,
        ], 203);
        if (!Hash::check($request->password, $user->password)) return response()->json([
            'message' => 'Password is incorrect',
            'status' => 203,
        ], 203);
        $user->token = $user->createToken('token')->plainTextToken;
        return response()->json($user, 200);
    }
}
