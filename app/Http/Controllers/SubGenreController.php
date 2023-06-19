<?php

namespace App\Http\Controllers;

use App\Models\Subgenre;
use Illuminate\Http\Request;

class SubGenreController extends Controller
{
    public function index(Request $request)
    {
        $res = Subgenre::where('name', 'like', '%' . $request->q . '%')->paginate($request->get('perPage', 10));
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);
        $data = $request->only([
            'name'
        ]);
        Subgenre::create($data);
        return response()->json([
            'message' => 'Subgenre created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = Subgenre::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $res = Subgenre::find($id);
        $data = $request->only([
            'name',
            'status'
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'Subgenre updated successfully',
        ], 201);
    }
}
