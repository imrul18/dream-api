<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'claim_url',
        'claim_upc',
        'content_upc',
        'artist_channel_link',
        'artist_topic_link',
        'artist_upc1',
        'artist_upc2',
        'artist_upc3',
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
            2 => 'Approved',
            3 => 'Rejected',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
