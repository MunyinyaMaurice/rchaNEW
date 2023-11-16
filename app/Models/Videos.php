<?php

namespace App\Models;

use App\Models\Place;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Videos extends Model
{
    use HasFactory;
    protected $fillable = [
    'place_id',
    'self_guided_short_version',
    'short_eng_version_360_video',
    'short_french_version_360_video',
    'short_kiny_version_360_video',

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
