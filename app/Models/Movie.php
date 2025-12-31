<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    protected $fillable = [
        'name',
        'poster_image',
        'country',
        'year',
        'duration',
        'description',
        'average_rating',
        'film_rank',
        'film_popularity_rank',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'movie_actor');
    }
}
