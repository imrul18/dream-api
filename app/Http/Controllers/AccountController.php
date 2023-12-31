<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $data = Transaction::with('bank')->where('type', $request->type);
        if (isset($request->q)) {
            $data->whereHas('user', function ($item) use ($request) {
                $item->where('first_name', 'like', '%' . $request->q . '%')->orWhere('last_name', 'like', '%' . $request->q . '%')->orWhere('username', 'like', '%' . $request->q . '%');
            });
        }
        if (isset($request->status)) $data = $data->where('status', $request->status);
        if (isset($request->user)) $data = $data->where('user_id', $request->user);
        $data = $data->latest()->paginate($request->get('perPage', 10));

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'for_month' => 'required',
            'for_year' => 'required',
            'user_id' => 'required',
        ], [], [
            'user_id' => 'User',
        ]);
        $data = $request->only([
            'amount',
            'for_month',
            'for_year',
            'user_id'
        ]);
        $data['type'] = 'credit';
        $data['status'] = 'Pending';
        Transaction::create($data);
        // Account::where('user_id', $request->user_id)->increment('balance', (int)$request->amount);

        // $header = 'Create a new payment';
        // $message = 'The following payment has been created';
        // $title = 'For - ' . $request->for_month . '-' . $request->for_year . ' and Amount - ' . $request->amount;
        // $reason = null;
        // sendMailtoUser(User::find($request->user_id), $header, $message, $title, $reason);
        return response()->json([
            'message' => 'Audio created successfully',
            'status' => 201
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $res = Transaction::find($id);
        $request->validate([
            'status' => 'required',
        ]);
        $header = 'Withdraw status updated';
        $message = 'The following Withdraw status has been Changed';
        $title = 'For - ' . $request->for_month . '-' . $request->for_year . ' and Amount - ' . $request->amount;
        $reason = null;

        if (($res->status == "Rejected" || $res->status == "Approved") && $request->status == "Pending") {
            return response()->json([
                'message' => 'Can not change status to Pending',
                'status' => 203,
            ], 203);
        } elseif ($request->type == 'credit') {
            if ($request->status == "Rejected") {
                $data = $request->only([
                    'status',
                ]);
                if ($res->status == "Approved") {
                    Account::where('user_id', $res->user_id)->decrement('balance', $res->amount);
                    $header = 'Cancel a previous payment';
                    $message = 'The following payment has been calceled';
                    $reason = null;
                }
                $res->update($data);
            } elseif ($request->status == "Approved") {
                $data = $request->only([
                    'status',
                ]);
                Account::where('user_id', $res->user_id)->increment('balance', $res->amount);
                $header = 'Create a new payment';
                $message = 'The following payment has been created';
                $reason = null;
                if ($res->status == "Rejected") {
                    $header = 'Update a previous payment';
                    $message = 'The following payment has been updated';
                    $reason = null;
                }
                $res->update($data);
            }
        } elseif ($request->type == 'debit') {
            if ($request->status == "Approved") {
                $request->validate([
                    'file_url' => 'required',
                ], [], [
                    'file_url' => 'File',
                ]);
                $data = $request->only([
                    'status',
                ]);
                File::makeDirectory(public_path('uploads/withdraw'), 0777, true, true);
                if ($request->hasFile('file_url')) {
                    $file = $request->file('file_url');
                    $filename = random_int(100000, 999999) . '_' . $request->project_id . '_' . $file->getClientOriginalName();
                    $location = 'uploads/withdraw';
                    $file->move($location, $filename);
                    $filepath = $location . "/" . $filename;
                    $data['file_url'] = $filepath;
                }
                if ($res->status == "Rejected") {
                    Account::where('user_id', $res->user_id)->decrement('balance', $res->amount);
                }
                $res->update($data);
                $header = 'Withdraw request Approved';
                $message = 'The following withdraw request has been Approved';
            } elseif ($request->status == "Rejected") {
                $request->validate([
                    'note' => 'required',
                ], [], [
                    'note' => 'Note',
                ]);
                $data = $request->only([
                    'status',
                ]);
                Account::where('user_id', $res->user_id)->increment('balance', $res->amount);
                $res->update($data);
                $header = 'Withdraw request Rejected';
                $message =  'The following withdraw request has been Rejected';
                $reason = $request->note;
            }
        }
        sendMailtoUser($res->user, $header, $message, $title, $reason);

        return response()->json([
            'message' => 'Transaction updated successfully',
            'status' => 201,
        ], 201);
    }

    public function withdrawBalance(Request $request)
    {
        $bank = BankAccount::where('isPrimary', 1)->first();
        Transaction::create([
            'user_id' => auth()->user()->id,
            'date' => date('Y-m-d'),
            'amount' => $request->amount,
            'type' => 'debit',
            'bank_id' => $bank->id,
            'status' => 'Pending',
        ]);
        Account::where('user_id', auth()->user()->id)->decrement('balance', $request->amount);
        sendMailtoAdmin(
            auth()->user(),
            'Create a New Windraw Request',
            'The following withdraw request has been created by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '(' . auth()->user()->username . ')',
            'Amount - ' . $request->amount,
        );

        return response()->json([
            'message' => 'Withdraw request sent successfully',
            'status' => 201
        ], 201);
    }

    public function overview(Request $request)
    {
        $transaction = Transaction::where('type', 'credit')->where('status', 'Approved');
        if (isset($request->year)) {
            $transaction = $transaction->whereYear('date', $request->year);
        }
        $transaction = $transaction->latest()->paginate(1000);
        return response()->json([
            'transaction' => $transaction,
        ], 200);
    }
}
