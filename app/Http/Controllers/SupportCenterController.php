<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportCenterController extends Controller
{
    public function index(Request $request)
    {
        $data = SupportTicket::where('title', 'like', '%' . $request->q . '%');
        if (isset($request->status)) $data = $data->where('status', $request->status);
        if (isset($request->user)) $data = $data->where('user_id', $request->user);
        $data = $data->paginate($request->get('perPage', 10));
        return response()->json($data, 200);
    }

    public function show(string $id)
    {
        $data = SupportTicket::find($id);
        if ($data) {
            $messages = SupportMessage::where('support_ticket_id', $data->id)->get();
            $data->messages = $messages;
        }
        return response()->json($data, 200);
    }

    public function update(Request $request, string $id)
    {
        $res = SupportTicket::find($id);
        $request->validate([
            'status' => 'required|in:1,2,3',
        ]);
        $data = $request->only([
            'status',
            'note'
        ]);
        $res->update($data);

        return response()->json([
            'message' => "Ticket Status Updated Successfully",
            'status' => 201,
        ], 201);
        return response()->json($data, 201);
    }

    public function sendMessageFromAdmin(Request $request)
    {
        $data = $request->only([
            'support_ticket_id',
            'message',
        ]);
        $data['sender'] = 2;
        SupportMessage::create($data);

        $res = SupportTicket::find($request->support_ticket_id);
        if ($res->status == 1) {
            $data = ['status' => 2];
            $res->update($data);
        }
        return response()->json([
            'message' => "Message Sent Successfully",
            'status' => 201,
        ], 200);
    }

    public function sendMessageFromUser(Request $request)
    {
        $data = $request->only([
            'support_ticket_id',
            'message',
        ]);
        $data['sender'] = 1;
        SupportMessage::create($data);
        return response()->json([
            'message' => "Message Sent Successfully",
            'status' => 201,
        ], 200);
    }
}
