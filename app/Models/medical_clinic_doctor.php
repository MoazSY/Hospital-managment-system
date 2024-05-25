<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class medical_clinic_doctor extends Model
{
    use HasFactory;
    protected $table='medical_clinic_doctor';
    protected $fillable=[
        'medical_clinic_id',
        'doctors_id',
        'price',
        'start_time',
        'end_time',
        'days'

    ];
    protected $casts=[
        'days'=>'array'
    ];

    public function medical_clinc()
    {
        return $this->belongsTo(Medical_clinic::class, 'medical_clinic_id');
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }
}
