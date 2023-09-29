<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'title',
        'content',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => asset('/storage/posts/' . $image),
        );
    }
    
}
