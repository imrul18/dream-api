<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function index(Request $request)
    {
        $res = Artist::where('title', 'like', '%' . $request->q . '%')->paginate($request->get('perPage', 10));
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'title' => 'required',
        ]);
        $data = $request->only([
            'user_id',
            'title',
            'spotify_url',
            'apple_url',
            'facebook_url',
            'instragram_url',
            'youtube_url',
        ]);
        $data['status'] = 2;
        Artist::create($data);
        return response()->json([
            'message' => 'Artist created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = Artist::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $res = Artist::find($id);
        $data = $request->only([
            'title',
            'spotify_url',
            'apple_url',
            'facebook_url',
            'instragram_url',
            'youtube_url',
            'status',
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'Artist updated successfully',
        ], 201);
    }

    //for user
    public function userIndex(Request $request)
    {
        $res = Artist::where('title', 'like', '%' . $request->q . '%')->paginate($request->get('perPage', 10));
        return response()->json($res, 200);
    }
    public function userStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);
        $data = $request->only([
            'title',
            'spotify_url',
            'apple_url',
            'facebook_url',
            'instragram_url',
            'youtube_url',
        ]);
        $data['user_id'] = auth()->user()->id;
        $data['status'] = 2;
        Artist::create($data);
        return response()->json([
            'message' => 'Artist created successfully',
        ], 201);
    }
    public function userUpdate(Request $request, string $id)
    {
        $res = Artist::find($id);
        $data = $request->only([
            'title',
            'spotify_url',
            'apple_url',
            'facebook_url',
            'instragram_url',
            'youtube_url',
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'Artist updated successfully',
        ], 201);
    }
}
