<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AudioFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'audio_id',
        'file_name',
        'file_url',
    ];

    protected $appends = ['file_download_url'];

    public function getFileDownloadUrlAttribute()
    {
        return asset($this->file_url);
    }
}
