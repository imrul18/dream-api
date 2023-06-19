<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $res = Genre::where('name', 'like', '%' . $request->q . '%')->paginate($request->get('perPage', 10));
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
        Genre::create($data);
        return response()->json([
            'message' => 'Genre created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = Genre::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $res = Genre::find($id);
        $data = $request->only([
            'name',
            'status'
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'Genre updated successfully',
        ], 201);
    }
}
