<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'message',
        'status',
        'sender'
    ];

    public function ticket()
    {
        return $this->belongsTo(supportTicket::class, 'ticket_id');
    }

    public function status()
    {
        return [
            1 => 'Sending',
            2 => 'Sent',
            3 => 'Deliveried',
            4 => 'Read',
        ];
    }

    public function sender()
    {
        return [
            1 => 'User',
            2 => 'Admin',
        ];
    }
}
