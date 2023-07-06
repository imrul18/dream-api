<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AudioImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'audio_id',
        'image_name',
        'image_url',
    ];

    protected $appends = ['image_download_url'];

    public function getImageDownloadUrlAttribute()
    {
        return asset($this->image_url);
    }
}
