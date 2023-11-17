<?php

namespace App\Models;

use App\Models\Place;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaidVideos extends Model
{
    use HasFactory;
    protected $fillable = [
        'place_id',
    'long_version_self_guided',
    'long_eng_version_360_video',
    'long_french_version_360_video',
    'long_kiny_version_360_video',
    ] ;
    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
