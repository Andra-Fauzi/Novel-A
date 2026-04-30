<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Content extends Model
{
    //
    use HasFactory;
    protected $guarded = [];

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }
}
