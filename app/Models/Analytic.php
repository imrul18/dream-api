<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'label_id',
        'status',
        'file_url',
    ];

    protected $appends = [
        'current_status', 'file_download_url'
    ];

    public function getCurrentStatusAttribute()
    {
        return $this->status()[$this->status];
    }

    public function getFileDownloadUrlAttribute()
    {
        return asset($this->file_url);
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
            return $model->with('label');
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

    public function label()
    {
        return $this->belongsTo(Label::class);
    }
}
