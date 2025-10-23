<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieSearch extends Model
{
    use HasFactory;

    protected $table = 'movie_searches';

    protected $fillable = [
        'ip_address',
        'search_request',
    ];
}
