<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'type',
        'prev_balance',
        'curr_balance',
    ];
}
