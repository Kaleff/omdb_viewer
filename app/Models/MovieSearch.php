<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieSearch extends Model
{
    protected $table = 'movie_searches';

    protected $fillable = [
        'ip_address',
        'search_request',
    ];
}
