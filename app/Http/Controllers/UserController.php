<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $res = User::where('isAdmin', false)->where('name', 'like', '%' . $request->q . '%')->paginate($request->get('perPage', 10));
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required'
        ]);
        $data = $request->only([
            'name',
            'email'
        ]);
        $data['password'] = Hash::make($request->password);
        $user = User::create($data);
        Account::create([
            'user_id' => $user->id,
            'balance' => 0
        ]);
        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = User::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $res = User::find($id);
        $data = $request->only([
            'name',
            'status'
        ]);
        if (isset($request->password)) $data['password'] = Hash::make($request->password);
        $res->update($data);
        return response()->json([
            'message' => 'Genre updated successfully',
        ], 201);
    }
}
