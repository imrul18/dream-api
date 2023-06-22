<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'status',
    ];

    protected $appends = ['current_status'];
    public function getCurrentStatusAttribute()
    {
        return $this->status()[$this->status];
    }

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($model) {
            if (!auth()->user()->isAdmin) {
                $model->where('user_id', auth()->user()->id);
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
            2 => 'Ongoing',
            3 => 'Complete',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }
}
