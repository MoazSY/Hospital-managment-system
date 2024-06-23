<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='patient';
    protected $guard='patient';
    protected $fillable=[
        'id_file',
        'name',
        'gender',
        'birthdate',
        'birth_address',
        'cur_address',
        'phoneNumber',
        'phoneNumber_near',
        'info_health_insurance',
        'NumberDocument_ins',
        'details_covering_ins',
        'condition_ins',
        'contact_info_companany'

];
public function details_visit()
{
    return $this->hasMany(Visit_details::class,'patient_id');
}

public function doctor_examination()
{
    return $this->hasMany(Doctor_examination::class,'patient_id');
}

public function radiology_report()
{
    return $this->hasMany(Radiology_report::class,'patient_id');
}
public function imaging_report()
{
    return $this->hasMany(Imaging_report::class,'patient_id');
}

public function queue_line()
{
    return $this->hasMany(Line_queue::class,'patient_id');
}

public function result_laboratory()
{
    return $this->hasMany(Result_Laboratory_anylysis::class,'patient_id');
}

public function medical_operation()
{
    return $this->hasMany(Medical_operation::class,'patient_id');
}
public function consumers()
{
    return $this->hasMany(Consumers::class,'patient_id');
}

public function bill()
{
    return $this->hasMany(Patient_bill::class,'patient_id');
}

public function room_stay()
{
    return $this->hasMany(Stay_operation_rooms::class,'patient_id');
}
public function laboratory_visit()
{
    return $this->hasOne(Visit_laboratory::class,'patient_id');
}
}

