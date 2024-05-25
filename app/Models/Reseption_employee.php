<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Reseption_employee extends Authenticatable
{
    use HasFactory;
    protected $table='reseption_employee';
    protected $guard='reseption_employee';
    protected $fillable=[
        'name',
        'image',
        'phoneNumber',
        'birthdate',
        'address',
        'userName',
        'password',
        'section_name',
        'email'
    ];

}

