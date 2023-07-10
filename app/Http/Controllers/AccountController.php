<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $data = Transaction::with('bank')->where('type', $request->type);
        if (isset($request->status)) $data = $data->where('status', $request->status);
        if (isset($request->user)) $data = $data->where('user_id', $request->user);
        $data = $data->paginate($request->get('perPage', 10));

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'date',
            'amount',
            'for',
            'user_id'
        ]);
        $data['type'] = 'credit';
        $data['status'] = 'Approved';
        Transaction::create($data);
        Account::where('user_id', $request->user_id)->increment('balance', (int)$request->amount);

        return response()->json([
            'message' => 'Audio created successfully',
            'status' => 201
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $res = Transaction::find($id);
        $data = $request->only([
            'status',
        ]);
        $res->update($data);
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
        return response()->json([
            'message' => 'Withdraw request sent successfully',
            'status' => 201
        ], 201);
    }

    public function overview(Request $request)
    {
        $transaction = Transaction::where('type', 'credit');
        if (isset($request->year)) {
            $transaction = $transaction->whereYear('date', $request->year);
        }
        $transaction = $transaction->paginate(1000);
        return response()->json([
            'transaction' => $transaction,
        ], 200);
    }
}
