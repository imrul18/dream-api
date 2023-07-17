<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $banks = BankAccount::get();
        $balance = Account::where('user_id', auth()->user()->id)->first();
        $transaction = Transaction::where('type', 'debit')->latest()->paginate(1000);
        $minimum = Setting::first()->min_withdraw;
        return response()->json([
            'banks' => $banks,
            'balance' => $balance,
            'minimum' => $minimum,
            'transaction' => $transaction
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'bank_name',
            'account_name',
            'account_number',
            'ifsc',
            'isPrimary',
        ]);
        $data['user_id'] = auth()->user()->id;
        if ($data['isPrimary'] == true) {
            $banks = BankAccount::get();
            foreach ($banks as $bank) {
                $bank->isPrimary = false;
                $bank->save();
            }
        }
        BankAccount::create($data);
        return response()->json([
            'message' => 'Bank account added successfully',
            'status' => 201
        ], 201);
    }

    public function update(Request $request, BankAccount $account)
    {
        return true;
    }

    public function activeBankAccount($id)
    {
        $banks = BankAccount::get();
        foreach ($banks as $bank) {
            $bank->isPrimary = false;
            $bank->save();
        }
        $bank = BankAccount::find($id);
        $bank->isPrimary = true;
        $bank->save();
        return response()->json([
            'message' => 'Bank account updated successfully',
            'status' => 200
        ], 200);
    }
}
