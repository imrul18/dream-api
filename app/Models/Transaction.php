<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'amount',
        'type',
        'for_month',
        'for_year',
        'bank_id',
        'status',
        'note',
        'file_url',
    ];

    protected $appends = [
        'file_download_url'
    ];

    public function getFileDownloadUrlAttribute()
    {
        return asset($this->file_url);
    }

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($model) {
            if (!auth()->user()->isAdmin) {
                $model->where('user_id', auth()->user()->id); //TODO uncomment this when auth is ready
            } else {
                $model->with('user');
            }
            return $model;
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function bank()
    {
        return $this->hasOne(BankAccount::class, 'id', 'bank_id');
    }
}
