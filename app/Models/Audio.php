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
            $model->with('language', 'genre', 'subgenre', 'label', 'format', 'parentalAdvisory', 'artists.artist', 'featurings', 'remixers', 'arrangers', 'producers', 'composers', 'files', 'images');
            if (!auth()->user()->isAdmin) {
                $model->where('user_id', auth()->user()->id); //TODO uncomment this when auth is ready
            } else {
                $model->with('user');
            }
            return $model;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function subgenre()
    {
        return $this->belongsTo(Subgenre::class);
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function parentalAdvisory()
    {
        return $this->belongsTo(ParentalAdvisory::class);
    }

    public function format()
    {
        return $this->belongsTo(Format::class);
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

    public function composers()
    {
        return $this->hasMany(AudioComposer::class);
    }

    public function files()
    {
        return $this->hasOne(AudioFile::class);
    }

    public function images()
    {
        return $this->hasOne(AudioImage::class);
    }
}
