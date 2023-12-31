<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallerTuneCrbt extends Model
{
    use HasFactory;

    protected $fillable = [
        'caller_tune_id',
        'crbt_id',
    ];

    public function crbt()
    {
        return $this->hasOne(Crbt::class, 'id', 'crbt_id');
    }
}
