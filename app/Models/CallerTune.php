<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallerTune extends Model
{
    use HasFactory;

    protected $fillable = [
        'audio_id',
        'is_requested',
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($model) {
            $model->with(['crbts', 'audio']);
            if (!auth()->user()->isAdmin) {
                $model->whereHas('audio', function ($audio) {
                    $audio->where('user_id', auth()->user()->id); //TODO uncomment this when auth is ready
                });
            }
        });
    }

    public function audio()
    {
        return $this->belongsTo(Audio::class);
    }

    public function crbts()
    {
        return $this->hasManyThrough(Crbt::class, CallerTuneCrbt::class, 'caller_tune_id', 'id', 'id', 'crbt_id');
    }
}
