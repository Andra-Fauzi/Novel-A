<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Novel extends Model
{
    //
    use HasFactory;
    protected $guarded = [];

    public function author() {
        return $this->belongsTo(Author::class);
    }

    public function volumes() {
        return $this->hasMany(Volume::class);
    }

    public function recommend() {
        return $this->hasOne(Recommend::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function genres() {
        return $this->belongsToMany(Genre::class);
    }

    public function categories() {
        return $this->belongsToMany(category::class);
    }

    public function likedByUsers() {
        return $this->belongsToMany(User::class, 'user_novel_likes')->withTimestamps();
    }

    public function starredByUsers() {
        return $this->belongsToMany(User::class, 'user_novel_stars')->withTimestamps();
    }
}
