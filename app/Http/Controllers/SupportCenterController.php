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
        $data = $data->latest()->paginate($request->get('perPage', 10));
        return response()->json($data, 200);
    }

    public function userIndex(Request $request)
    {
        $data = SupportTicket::where('title', 'like', '%' . $request->q . '%');
        $data = $data->latest()->paginate($request->get('perPage', 1000));
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

    public function store(Request $request)
    {
        $data = $request->only([
            'title',
        ]);
        $data['status'] = 1;
        $data['user_id'] = $request->user()->id;
        $support = SupportTicket::create($data);
        $message = $request->only([
            'message',
        ]);
        $message['sender'] = 1;
        $message['support_ticket_id'] = $support->id;
        SupportMessage::create($message);

        sendMailtoAdmin(
            auth()->user(),
            'A New Support Ticket has been created',
            'The following Support Ticket has been created by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '(' . auth()->user()->username . ')',
            $request->title
        );

        return response()->json([
            'message' => 'Ticket created successfully',
            'status' => 201
        ], 201);
    }

    public function sms(Request $request, string $id)
    {
        $res = SupportTicket::find($id);
        $res['unread_for_user'] = 0;
        $res->save();
        $sms = [];
        $data = SupportMessage::where('support_ticket_id', $id)->get();
        if (!isset($request->type) || $request->type != 'all') {
            $data = $data->take(-4);
            foreach ($data as $key => $value) {
                $sms[] = $value;
            }
        } else {
            $sms = $data;
        }
        return response()->json($sms, 200);
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
        $res['updated_at'] = now();
        if ($res->status == 1) {
            $data = ['status' => 2];
        }
        $res['unread_for_user'] = $res->unread_for_user + 1;
        $res['unread_for_admin'] = 0;
        $res->update($data);

        sendMailtoUser($res->user, 'A New Message has been sent', 'The following message has been sent by Admin for ticket ' . $res->title, $request->message, null);
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
        $res = SupportTicket::find($request->support_ticket_id);
        $res['updated_at'] = now();
        $res['unread_for_user'] = 0;
        $res['unread_for_admin'] = $res->unread_for_admin + 1;
        $res->update($data);
        return response()->json([
            'message' => "Message Sent Successfully",
            'status' => 201,
        ], 200);
    }
}
