<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        $res = Language::where('name', 'like', '%' . $request->q . '%')->paginate($request->get('perPage', 10));
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
        Language::create($data);
        return response()->json([
            'message' => 'Language created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = Language::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $res = Language::find($id);
        $request->validate([
            'name' => 'required',
        ]);
        $data = $request->only([
            'name',
            'status'
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'Language updated successfully',
        ], 201);
    }
}
