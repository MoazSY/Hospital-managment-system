<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical_clinic extends Model
{
    use HasFactory;
    protected $table='medical_clinic';
    protected $fillable=[
        'name',
        'start_time',
        'end_time',
        'days',
        'address',
        'contact_info',
        'info_clinic'
    ];
    protected $casts=[
        'days'=>'array'
    ];


public function medical_doctor()
{
    return $this->hasMany(medical_clinic_doctor::class);
}
}

