<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index(Request $request)
    {
        $res = Label::with('user')->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->q . '%')
                ->orWhereHas('user', function ($user) use ($request) {
                    $user->where('name', 'like', '%' . $request->q . '%');
                });
        });

        if ($request->status) $res->where('status', $request->status);
        if ($request->user) $res->where('user_id', $request->user);

        $res = $res->paginate($request->get('perPage', 10));
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'title' => 'required',
        ], [
            'user_id.required' => 'User is required.',
        ]);
        $data = $request->only([
            'user_id',
            'title',
            'youtube_url',
            'message',
        ]);
        $data['status'] = 2;
        Label::create($data);
        return response()->json([
            'message' => 'Label created successfully',
        ], 201);
    }

    public function show(string $id)
    {
        $res = Label::find($id);
        return response()->json($res, 200);
    }

    public function update(Request $request, string $id)
    {
        $res = Label::find($id);
        $data = $request->only([
            'title',
            'youtube_url',
            'message',
            'status',
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'Label updated successfully',
        ], 201);
    }

    //for user
    public function userIndex(Request $request)
    {
        $res = Label::where('title', 'like', '%' . $request->q . '%');
        if ($request->requested) $res->where('status', $request->requested);
        // $res = $res->get();
        $res = $res->paginate($request->get('perPage', 1000));
        return response()->json($res, 200);
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);
        $data = $request->only([
            'title',
            'youtube_url',
            'message',
        ]);
        $data['user_id'] = auth()->user()->id;
        $data['status'] = 1;
        Label::create($data);
        return response()->json([
            'message' => 'Label created successfully',
            'status' => 201
        ], 201);
    }

    public function userUpdate(Request $request, string $id)
    {
        $res = Label::find($id);
        $data = $request->only([
            'title',
            'youtube_url',
            'message',
        ]);
        $res->update($data);
        return response()->json([
            'message' => 'Label updated successfully',
        ], 201);
    }
}
