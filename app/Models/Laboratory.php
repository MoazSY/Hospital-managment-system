<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Laboratory extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='laboratorys';
    protected $guard='laboratory';
    protected $fillable=[
        'name',
        'image',
        'birthdate',
        'about_him',
        'phoneNumber',
        'userName',
        'password',
        'email',
    'address'    ];


        public function result_anylysis()
        {
            return $this->hasMany(Result_Laboratory_anylysis::class);
        }
}
