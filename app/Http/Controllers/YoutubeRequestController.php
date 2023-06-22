<?php

namespace App\Http\Controllers;

use App\Models\YoutubeRequest;
use Illuminate\Http\Request;

class YoutubeRequestController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
        $data = [];
        if (isset($type)) {
            $data = YoutubeRequest::where('type', $type);
            if (isset($request->status)) $data = $data->where('status', $request->status);
            if (isset($request->user)) $data = $data->where('user_id', $request->user);

            if (isset($request->q)) {
                if ($type == 1) $data = $data->where('claim_upc', 'like', '%' . $request->q . '%');
                if ($type == 2) $data = $data->where('content_upc', 'like', '%' . $request->q . '%');
                if ($type == 3) $data = $data->where('artist_upc1', 'like', '%' . $request->q . '%');
            }

            $data = $data->paginate($request->get('perPage', 10));
        }
        return response()->json($data, 200);
    }

    public function update(Request $request, string $id)
    {
        $res = YoutubeRequest::find($id);
        $request->validate([
            'status' => 'required|in:1,2,3',
        ]);
        $data = $request->only([
            'status',
        ]);
        $res->update($data);

        return response()->json([
            'message' => "Request Updated Successfully",
            'status' => 201,
        ], 201);
        return response()->json($data, 201);
    }
}
