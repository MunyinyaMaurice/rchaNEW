<?php

namespace App\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Token extends Model
{
    use HasFactory;
    protected $fillable = [
        'paid_token',
        'token_expires_at',
        'paid_link',
        //'created_at',
    ];
    protected $guarded = [];
    public function payment()
    {
        return $this->hasMany(Payment::class);
    }
}
