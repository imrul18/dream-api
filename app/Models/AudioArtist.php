<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AudioArtist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'audio_id',
        'artist_id',
        'isPrimary',
        'sequence_number'
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
}
