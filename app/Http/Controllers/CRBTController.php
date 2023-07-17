<?php

namespace App\Http\Controllers;

use App\Models\Crbt;
use Illuminate\Http\Request;

class CRBTController extends Controller
{
    public function index(Request $request)
    {
        $res = Crbt::where('title', 'like', '%' . $request->q . '%')->latest()->paginate($request->get('perPage', 10));
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);
        $data = $request->only([
            'title',
            'icon',
        ]);
        Crbt::create($data);
        return response()->json([
            'message' => 'CRBT created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = Crbt::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required',
        ]);
        $res = Crbt::find($id);
        $data = $request->only([
            'title',
            'icon',
            'status'
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'CRBT updated successfully',
        ], 201);
    }
}
