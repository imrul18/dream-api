<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_name',
        'account_number',
        'ifsc',
        'isPrimary',
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($model) {
            if (!auth()->user()->isAdmin) {
                $model->where('user_id', auth()->user()->id); //TODO uncomment this when auth is ready
            }
            return $model;
        });
    }
}
