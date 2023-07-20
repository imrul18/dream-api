<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\DatabaseNotification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // $request->validate([
        //     'email' => 'required',
        //     'password' => 'required',
        // ]);
        if (!isset($request->email)) return response()->json([
            'message' => 'username/Email can not be Empty',
            'status' => 203
        ], 203);
        $user = User::where('isAdmin', true)->where(function ($item) use ($request) {
            $item->where('email', $request->email)->orWhere('username', $request->email);
        })->first();
        if (!$user) return response()->json([
            'message' => 'User not found',
            'status' => 203,
        ], 203);
        if (!Hash::check($request->password, $user->password)) return response()->json([
            'message' => 'Password is incorrect',
            'status' => 203,
        ], 203);
        $user->token = $user->createToken('token')->plainTextToken;
        $user->status = 200;
        return response()->json($user, 200);
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $user = User::where('isAdmin', true)->where(function ($item) use ($request) {
            $item->where('email', $request->email)->orWhere('username', $request->email);
        })->first();
        if (!$user) return response()->json([
            'message' => 'User not found',
            'status' => 203,
        ], 203);
        if (!Hash::check($request->password, $user->password)) return response()->json([
            'message' => 'Password is incorrect',
            'status' => 203,
        ], 203);
        $user->token = $user->createToken('token')->plainTextToken;
        return response()->json($user, 200);
    }

    public function notification()
    {
        $notifications = DatabaseNotification::where('notifiable_id', auth()->user()->id)->latest()->take(5)->get()->pluck('data');
        $settings = Setting::first();
        return response()->json([
            'notifications' => $notifications,
            'settings' => $settings,
            'status' => 200,
        ], 200);
    }

    public function dashboard()
    {
        $count = [];
        $count['total'] = Audio::count();
        $count['pending'] = Audio::where('status', 1)->count();
        $count['approved'] = Audio::where('status', 3)->count();
        $data = [];
        $data['approved'] = Audio::where('status', 3)->latest()->take(4)->get();
        $data['pending'] = Audio::where('status', 1)->latest()->take(4)->get();
        $data['draft'] = Audio::where('status', 4)->latest()->take(4)->get();
        $res = [
            'count' => $count,
            'data' => $data,
        ];
        return response()->json($res, 200);
    }

    public function profileUpdate(Request $request)
    {
        $data = $request->only([
            'first_name',
            'last_name',
            'phone',
            'country',
            'city',
            'state',
            'postal_address',
            'postal_code',
        ]);

        $res = User::find(auth()->user()->id);
        $res->update($data);
        return response()->json(User::find(auth()->user()->id), 200);
    }

    public function imageUpdate(Request $request)
    {
        $data = $request->only([
            'file',
        ]);

        $res = User::find(auth()->user()->id);

        File::makeDirectory(public_path('uploads/profile'), 0777, true, true);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = random_int(100000, 999999) . '_' . $request->project_id . '_' . $file->getClientOriginalName();
            $location = 'uploads/profile';
            $file->move($location, $filename);
            $filepath = $location . "/" . $filename;
            $data['profile_image'] = $filepath;
        }
        $res->update($data);
        return response()->json(User::find(auth()->user()->id), 200);
    }
}
