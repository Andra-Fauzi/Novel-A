<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chapter extends Model
{
    //
    use HasFactory;
    protected $guarded = [];

    public function volume() {
        return $this->belongsTo(Volume::class);
    }

    public function content() {
        return $this->hasOne(Content::class);
    }
}
