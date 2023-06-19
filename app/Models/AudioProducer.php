<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AudioProducer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'audio_id',
        'producer',
        'isPrimary',
        'sequence_number'
    ];
}
