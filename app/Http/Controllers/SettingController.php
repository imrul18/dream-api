<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return response()->json([
            'data' => $settings,
            'status' => 200,
        ], 200);
    }

    public function update(Request $request)
    {
        $settings = Setting::first();
        $settings->whatsapp = $request->whatsapp;
        $settings->min_withdraw = $request->min_withdraw;
        $settings->save();

        return response()->json([
            'message' => "Settings updated successfully",
            'status' => 201,
        ], 201);
    }
}
