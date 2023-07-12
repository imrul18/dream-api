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
        $res = User::where('isAdmin', false)->where('first_name', 'like', '%' . $request->q . '%')->orWhere('last_name', 'like', '%' . $request->q . '%')->orWhere('govt_id', 'like', '%' . $request->q . '%')->orWhere('username', 'like', '%' . $request->q . '%')->orWhere('email', 'like', '%' . $request->q . '%')->paginate($request->get('perPage', 10));
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'govt_id' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
            'password' => 'required'
        ]);
        $data = $request->only([
            'first_name',
            'last_name',
            'govt_id',
            'username',
            'email',
            'password'
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
            'first_name' => 'required',
            'last_name' => 'required',
            'govt_id' => 'required',
        ]);
        $data = $request->only([
            'first_name',
            'last_name',
            'govt_id',
        ]);
        if (isset($request->password)) $data['password'] = Hash::make($request->password);
        $res = User::find($id);
        $res->update($data);
        return response()->json([
            'message' => 'Genre updated successfully',
        ], 201);
    }
}
