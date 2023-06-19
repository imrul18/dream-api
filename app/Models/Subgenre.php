<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subgenre extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'genre_id',
        'name',
        'status'
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
