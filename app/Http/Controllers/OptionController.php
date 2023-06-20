<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Language;
use App\Models\ParentalAdvisory;
use App\Models\User;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function user()
    {
        $res = User::where('isAdmin', false)->get();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                'value' => $value->id,
                'label' => $value->name,
            ];
        }
        return response()->json($data);
    }
    public function artist()
    {
        // TODO change this to auth()->user()->id when auth is ready
        $res = Artist::where('user_id', 1)->where('status', 2)->get();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                'value' => $value->id,
                'label' => $value->title,
            ];
        }
        return response()->json($data);
    }

    public function language()
    {
        $res = Language::where('status', true)->get();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                'value' => $value->id,
                'label' => $value->name,
            ];
        }
        return response()->json($data);
    }

    public function genre()
    {
        $res = Genre::where('status', true)->get();
        $data = [];
        foreach ($res as $value) {
            $subdata = [];
            foreach ($value->subgenres()->where('status', true)->get() as $subgenre) {
                $subdata[] = [
                    'value' => $subgenre->id,
                    'label' => $subgenre->name,
                ];
            }
            $data[] = [
                'value' => $value->id,
                'label' => $value->name,
                'subgenres' => $subdata
            ];
        }
        return response()->json($data);
    }

    public function label()
    {
        // TODO change this to auth()->user()->id when auth is ready
        $res = Label::where('user_id', 1)->where('status', 2)->get();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                'value' => $value->id,
                'label' => $value->title,
            ];
        }
        return response()->json($data);
    }

    public function format()
    {
        $res = Format::where('status', true)->get();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                'value' => $value->id,
                'label' => $value->name,
            ];
        }
        return response()->json($data);
    }

    public function parentalAdvisory()
    {
        $res = ParentalAdvisory::where('status', true)->get();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                'value' => $value->id,
                'label' => $value->name,
            ];
        }
        return response()->json($data);
    }
}
