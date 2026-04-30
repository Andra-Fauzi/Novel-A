<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    //
    use HasFactory;
    protected $guarded = [];

    public function novel() {
        return $this->belongsTo(Novel::class);
    }

    public function chapters() {
        return $this->hasMany(Chapter::class);
    }
}
