<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Hospital_manager extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $guard='hospital_manager';
    protected $table='hospital_manager';
    protected $fillable=[
        'name',
        'image',
        'birthdate',
        'phoneNumber',
        'about_him',
        'userName',
        'password',
        'email',
        'address'
    ];
}

