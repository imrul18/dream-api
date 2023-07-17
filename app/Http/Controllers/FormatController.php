<?php

namespace App\Http\Controllers;

use App\Models\Format;
use Illuminate\Http\Request;

class FormatController extends Controller
{
    public function index(Request $request)
    {
        $res = Format::where('name', 'like', '%' . $request->q . '%')->latest()->paginate($request->get('perPage', 10));
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $data = $request->only([
            'name'
        ]);
        Format::create($data);
        return response()->json([
            'message' => 'Format created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = Format::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $res = Format::find($id);
        $data = $request->only([
            'name',
            'status'
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'Format updated successfully',
        ], 201);
    }
}
