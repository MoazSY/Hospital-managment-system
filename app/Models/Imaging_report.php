<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imaging_report extends Model
{
    use HasFactory;
    protected $table='imaging_report';
    protected $fillable=[
        'name_image',
        'magnetic_resonnance_imaging_id',
        'doctors_id',
        'patient_id',
        'image',
        'price',
        'medical_diagnosis'
    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }

    public function magnetic_imaging()
    {
        return $this->belongsTo(Magnetic_resonnance_imaging::class, 'magnetic_resonnance_imaging_id');
    }
    protected $casts=[
        'image'=>'array'
    ];
}


