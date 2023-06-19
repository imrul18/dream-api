<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'subtitle',
        'writter',
        'main_release_date',
        'original_release_date',
        'language_id',
        'genre_id',
        'subgenre_id',
        'label_id',
        'format_id',
        'p_line',
        'c_line',
        'upc',
        'isrc',
        'parental_advisory_id',
        'producer_catalogue_number',
        'is_coller_tune',
        'status',
        'note',
    ];

    protected $appends = [
        'current_status'
    ];

    public function getCurrentStatusAttribute()
    {
        return $this->status()[$this->status];
    }

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($model) {
            return $model
                ->with('artists', 'featurings', 'remixers', 'arrangers', 'producers', 'files', 'images')
                // ->where('user_id', auth()->user()->id); //TODO uncomment this when auth is ready
                ->where('user_id', 1);
        });
    }

    public function status()
    {
        return [
            1 => 'Pending',
            2 => 'Draft',
            3 => 'Approved',
            4 => 'Rejected',
        ];
    }

    public function artists()
    {
        return $this->hasMany(AudioArtist::class);
    }

    public function featurings()
    {
        return $this->hasMany(AudioFeaturing::class);
    }

    public function remixers()
    {
        return $this->hasMany(AudioRemixer::class);
    }

    public function arrangers()
    {
        return $this->hasMany(AudioArranger::class);
    }

    public function producers()
    {
        return $this->hasMany(AudioProducer::class);
    }

    public function files()
    {
        return $this->hasMany(AudioFile::class);
    }

    public function images()
    {
        return $this->hasMany(AudioImage::class);
    }
}
