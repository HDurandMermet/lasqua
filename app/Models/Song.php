<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'artist', 'release_year', 'url', 'genre_id', 'user_id'
    ];
}
