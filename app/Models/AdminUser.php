<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticable;

class AdminUser extends Authenticable
{
    use HasApiTokens, HasFactory;
    protected $fillable=[
        'username',
        'password'
    ];

    protected $hidden = [     //To make the backend more secure
        'password',
        'remember_token',
    ];
}
