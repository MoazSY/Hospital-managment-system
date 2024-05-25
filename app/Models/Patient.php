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
    return $this->hasMany(Visit_details::class);
}

public function doctor_examination()
{
    return $this->hasMany(Doctor_examination::class);
}


public function imaging_report()
{
    return $this->hasMany(Imaging_report::class);
}

public function queue_line()
{
    return $this->hasMany(Line_queue::class);
}

public function result_laboratory()
{
    return $this->hasMany(Result_Laboratory_anylysis::class);
}

public function medical_operation()
{
    return $this->hasMany(Medical_operation::class);
}
public function consumers()
{
    return $this->hasMany(Consumers::class);
}

public function bill()
{
    return $this->hasMany(Patient_bill::class);
}

public function room_stay()
{
    return $this->hasMany(Stay_operation_rooms::class);
}
}

