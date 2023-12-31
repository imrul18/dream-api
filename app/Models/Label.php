<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'youtube_url',
        'message',
        'status',
    ];

    protected $appends = [
        'current_status'
    ];

    public function getCurrentStatusAttribute()
    {
        return $this->status()[$this->status];
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

    public function status()
    {
        return [
            1 => 'Pending',
            2 => 'Approved',
            3 => 'Rejected',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
