<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Payment;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Place extends Model
{
    use HasFactory;
    protected $fillable = [
        'place_name',
        'place_location',
        'place_status',
        'place_details',
        'category_id',
        'place_preview_video',
        'place_link',
        'amount',
       
        
    ];
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
{
    return $this->hasMany(Image::class);
}
public function payment()
    {
        return $this->hasMany(Payment::class);
    }
}
