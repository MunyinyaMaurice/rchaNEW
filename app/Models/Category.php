<?php

namespace App\Models;

use App\Models\Place;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_discription',
        'category_name',
    ];
    protected $guarded = [];
    public function place()
{
    return $this->hasMany(Place::class);
}
}
