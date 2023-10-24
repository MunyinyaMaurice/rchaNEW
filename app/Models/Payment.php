<?php

namespace App\Models;

use App\Models\User;
use App\Models\Place;
use App\Models\Token;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'place_id',
        'token_id',
        'amount',
        
    ];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }
}
