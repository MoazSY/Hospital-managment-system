<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Doctor extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='doctors';
    protected $guard='doctor';
    protected $fillable=[
        'name',
        'image',
        'birthdate',
        'about_him',
        'userName',
        'password',
        'specialization',
        'contact_info',
        'phoneNumber',
        'email',
        'address'
    ];

    public function operation()
{
    return $this->hasMany(Operations::class);
}
public function medical_clinic_doctor()
{
    return $this->hasMany(medical_clinic_doctor::class);
}
public function operation_section()
{
    return $this->hasMany(Operation_section::class);
}
public function doctor_operation_section()
{
    return $this->hasMany(Doctor_operation_section::class);
}
public function doctor_examination()
{
    return $this->hasMany(Doctor_examination::class);
}
public function request_medical()
{
    return $this->hasMany(Request_medical_supplies::class);
}
public function imaging_report()
{
    return $this->hasMany(Imaging_report::class);
}
public function radiology_report()
{
    return $this->hasMany(Radiology_report::class);
}
public function radiology_section()
{
    return $this->hasMany(Radiation_section::class);
}
public function imaging_section()
{
    return $this->hasMany(Magnetic_resonnance_imaging::class);
}
}

