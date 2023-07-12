<?php

use App\Models\User;
use App\Notifications\AudioUpdate;
use Illuminate\Support\Facades\Notification;

function sendMailtoUser($to, $header, $message, $title, $reason)
{
    $content = [
        'header' => $header,
        'message' => $message,
        'title' => $title,
        'reason' => $reason,
        'toAdmin' => false,
    ];
    Notification::send($to, new AudioUpdate($to, $content));
}

function sendMailtoAdmin($from, $header, $message, $title)
{
    $content = [
        'header' => $header,
        'message' => $message,
        'title' => $title,
        'reason' => null,
        'toAdmin' => true,
    ];
    Notification::send(User::find(1), new AudioUpdate(User::find(1), $content));
}
