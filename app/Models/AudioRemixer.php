<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AudioRemixer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'audio_id',
        'remixer',
        'isPrimary',
        'sequence_number'
    ];
}
