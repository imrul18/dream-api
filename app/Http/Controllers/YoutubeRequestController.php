<?php

namespace App\Http\Controllers;

use App\Models\YoutubeRequest;
use Illuminate\Http\Request;

use function PHPSTORM_META\type;

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

            $data = $data->latest()->paginate($request->get('perPage', 10));
        }
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'type',
            'claim_url',
            'claim_upc',
            'content_upc',
            'artist_channel_link',
            'artist_topic_link',
            'artist_upc1',
            'artist_upc2',
            'artist_upc3',
        ]);

        $data['status'] = 1;
        $data['user_id'] = auth()->user()->id;

        YoutubeRequest::create($data);

        $type = "A New Request has been created";
        if ($request->type == 1) {
            $type = 'Claim Release - ' . $request->claim_upc;
        } elseif ($request->type == 2) {
            $type = 'Content ID Request - ' . $request->content_upc;
        } elseif ($request->type == 3) {
            $type = 'Artist Channel Request - ' . $request->artist_upc1;
        }

        sendMailtoAdmin(
            auth()->user(),
            'A New Request has been created',
            'The following request has been created by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '(' . auth()->user()->username . ')',
            $type
        );

        return response()->json([
            'message' => 'Request created successfully',
            'status' => 201
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $res = YoutubeRequest::find($id);
        $request->validate([
            'status' => 'required|in:1,2,3',
        ]);
        if ($request->status == 3) {
            $request->validate(
                [
                    'note' => 'required'
                ],
                [],
                [
                    'note' => 'Note',

                ]
            );
        }
        $data = $request->only([
            'status',
        ]);
        $res->update($data);

        $type = "A New Request has been created";
        if ($request->type == 1) {
            $type = 'Claim Release - ' . $res->claim_upc;
        } elseif ($request->type == 2) {
            $type = 'Content ID Request - ' . $res->content_upc;
        } elseif ($request->type == 3) {
            $type = 'Artist Channel Request - ' . $res->artist_upc1;
        }
        if ($request->status == 1) {
            $header = 'Request has been Pending';
            $message = 'The following request has been Pending';
        } elseif ($request->status == 2) {
            $header = 'Request has been Approved';
            $message = 'The following request has been Approved';
        } elseif ($request->status == 3) {
            $header = 'Request has been Rejected';
            $message = 'The following request has been Rejected';
        } else {
            $header = 'Request Status Changed';
            $message = 'The following request status has been changed';
        }
        sendMailtoUser($res->user, $header, $message, $type, $request->note ?? null);

        return response()->json([
            'message' => "Request Updated Successfully",
            'status' => 201,
        ], 201);
        return response()->json($data, 201);
    }
}
