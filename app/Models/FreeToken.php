<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'organisation_name',
        'organisation_email',
        'token_expires_at',
        'freeToken',
        
    ];
    protected $guarded = [];
}
