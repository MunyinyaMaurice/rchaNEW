<?php

namespace App\Models;

use App\Models\Place;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;
    protected $fillable = ['place_id','image_path'];
    protected $guarded = [];
    public function place()
{
    return $this->belongsTo(Place::class);
}

    
}
