<?php

namespace App\Http\Controllers;

use App\Models\ParentalAdvisory;
use Illuminate\Http\Request;

class ParentalAdvisoryController extends Controller
{
    public function index(Request $request)
    {
        $res = ParentalAdvisory::where('name', 'like', '%' . $request->q . '%')->latest()->paginate($request->get('perPage', 10));
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
        ParentalAdvisory::create($data);
        return response()->json([
            'message' => 'ParentalAdvisory created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = ParentalAdvisory::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $res = ParentalAdvisory::find($id);
        $data = $request->only([
            'name',
            'status'
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'ParentalAdvisory updated successfully',
        ], 201);
    }
}
