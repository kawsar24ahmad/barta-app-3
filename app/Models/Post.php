<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'slug',
        'author_id',
        'picture'
    ];


    public function author() {
        return $this->belongsTo(User::class);
    }

    protected function pictureUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => asset('storage/' . $this->picture)  
        );
    }
    

}
