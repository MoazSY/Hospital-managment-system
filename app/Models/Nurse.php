<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Nurse extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='nurses';
    protected $guard='nurse';
    protected $fillable=[
        'name',
        'image',
        'about_him',
        'birthdate',
        'phoneNumber',
        'userName',
        'password',
        'address'
    ];

    public function section()
    {
        return $this->hasOne(Nurse_section::class);
    }
}
