<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Format extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status'
    ];

    protected $appends = [
        'current_status'
    ];

    public function getCurrentStatusAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}
