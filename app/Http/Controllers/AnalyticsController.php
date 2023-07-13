<?php

namespace App\Http\Controllers;

use App\Models\Analytic;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $data = [];

        $data = Analytic::query();
        if (isset($request->q)) $data = $data->where('year', 'like', '%' . $request->q . '%')->orWhere('month', 'like', '%' . $request->q . '%');
        if (isset($request->user)) $data = $data->where('user_id', $request->user);
        if (isset($request->status)) $data = $data->where('status', $request->status);
        $data = $data->paginate($request->get('perPage', 10));

        return response()->json($data, 200);
    }

    public function userIndex(Request $request)
    {
        $data = [];

        $data = Analytic::query();
        if (isset($request->q)) $data = $data->where('year', 'like', '%' . $request->q . '%')->orWhere('month', 'like', '%' . $request->q . '%');
        if (isset($request->user)) $data = $data->where('user_id', $request->user);
        if (isset($request->status)) $data = $data->where('status', $request->status);
        $data = $data->paginate($request->get('perPage', 1000));

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'month' => 'required',
            'label_id' => 'required',
        ], [], [
            'label_id' => 'Label',
        ]);
        $data = $request->only([
            'year',
            'month',
            'label_id',
        ]);
        $data['status'] = 1;
        $data['user_id'] = auth()->user()->id;

        Analytic::create($data);

        sendMailtoAdmin(
            auth()->user(),
            'A New Analytics Request has been created',
            'The following Analytics request has been created by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '(' . auth()->user()->username . ')',
            $request->year . '-' . $request->month . '-' . Label::find($request->label_id)->title,
        );

        return response()->json([
            'message' => 'Analytic created successfully',
            'status' => 201
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $res = Analytic::find($id);
        $request->validate([
            'status' => 'required',
        ]);

        $header = 'Analytics status updated';
        $message = 'The following analytics status has been Changed';
        $title = $res->year . '-' . $res->month . '-' . Label::find($res->label_id)->title;
        $reason = null;

        if ($request->status == 2) {
            $request->validate([
                'file_url' => 'required',
            ], [], [
                'file_url' => 'File',
            ]);
            $data = $request->only([
                'status',
            ]);
            File::makeDirectory(public_path('uploads/analytics'), 0777, true, true);
            if ($request->hasFile('file_url')) {
                $file = $request->file('file_url');
                $filename = random_int(100000, 999999) . '_' . $request->project_id . '_' . $file->getClientOriginalName();
                $location = 'uploads/analytics';
                $file->move($location, $filename);
                $filepath = $location . "/" . $filename;
                $data['file_url'] = $filepath;
            }
            $res->update($data);
            $header = 'Analytics Approved';
            $message = 'The following analytics has been Approved';
        }
        if ($request->status == 3) {
            $request->validate([
                'note' => 'required',
            ], [], [
                'note' => 'Note',
            ]);
            $data = $request->only([
                'status',
            ]);
            $res->update($data);
            $header = 'Analytics Rejected';
            $message =  'The following analytics has been Rejected';
            $reason = $request->note;
        }
        sendMailtoUser($res->user, $header, $message, $title, $reason);


        return response()->json([
            'message' => "Request Updated Successfully",
            'status' => 201,
        ], 201);
    }
}
