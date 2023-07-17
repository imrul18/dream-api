<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\CallerTune;
use App\Models\CallerTuneCrbt;
use Illuminate\Http\Request;

class CallerTuneController extends Controller
{
    public function index(Request $request)
    {
        $data = CallerTune::whereHas('audio', function ($audio) use ($request) {
            $audio->where('title', 'like', '%' . $request->q . '%');
            if (isset($request->user)) $audio = $audio->where('user_id', $request->user);
        });
        if (isset($request->status)) $data = $data->where('is_requested', $request->status);
        $data = $data->latest()->paginate($request->get('perPage', 10));
        return response()->json($data, 200);
    }

    public function userIndex(Request $request)
    {
        $data = CallerTune::whereHas('audio', function ($audio) use ($request) {
            $audio->where('title', 'like', '%' . $request->q . '%');
        });
        $data = $data->latest()->paginate($request->get('perPage', 1000));
        return response()->json($data, 200);
    }


    public function update(Request $request, string $id)
    {
        $callerTune = CallerTune::where('audio_id', $id)->first();
        $callerTune->update(['is_requested' => $request->is_caller_tune]);
        $res = Audio::find($id);
        $request->validate([
            'is_caller_tune' => 'required|in:0,1',
        ]);
        $data = $request->only([
            'is_caller_tune',
        ]);
        $res->update($data);

        if ($request->is_caller_tune == 0) {
            $header = 'Caller tune Pending';
            $message = 'The following caller tune has been Pending';
        } elseif ($request->is_caller_tune == 1) {
            $header = 'Caller tune Approved';
            $message =  'The following caller tune has been Approved';
        } else {
            $header = 'Caller tune Status Changed';
            $message =  'The following caller tune status has been changed';
        }
        sendMailtoUser($res->user, $header, $message, $res->title, $reason ?? null);

        return response()->json([
            'message' => 'Approved successfully',
            'status' => 201,
        ], 201);
    }

    //for user
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $call_tune = CallerTune::create([
            'audio_id' => $request->id,
            'is_requested' => 0,
        ]);
        foreach ($request->crbt_ids ?? [] as $crbt_id) {
            CallerTuneCrbt::create([
                'caller_tune_id' => $call_tune->id,
                'crbt_id' => $crbt_id,
            ]);
        }

        $audio = Audio::find($request->id);

        sendMailtoAdmin(
            auth()->user(),
            'Request for caller tune',
            'The following caller tune request has been created by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '(' . auth()->user()->username . ')',
            $audio->title
        );

        return response()->json([
            'message' => 'Apply Successfully',
            'status' => 203
        ], 203);
    }
}
