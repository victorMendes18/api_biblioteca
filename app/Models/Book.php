<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'year_of_publication',
        'number_of_pages',
        'public'
    ];
}
