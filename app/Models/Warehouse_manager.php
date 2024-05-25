<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Warehouse_manager extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='warehouse_manager';
    protected $guard='warehouse_manager';
    protected $fillable=[
        'name',
        'image',
        'birthdate',
        'about_him',
        'phoneNumber',
        'userName',
        'password',
        'email'
    ];

    public function pharmatical_warehouse()
    {
        return $this->hasOne(Pharmatical_warehouse::class);
    }
}
