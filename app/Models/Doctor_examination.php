<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor_examination extends Model
{
    use HasFactory;
    protected $table='doctor_examination';
    protected $fillable=[
        'doctors_id',
        'patient_id',
        'medical_history',
        'previous_illnesses',
        'Current_symptoms',
        'Symptoms_appear',
        'Medications_taken',
        'id_medical_examination',
        'laboratory_analysis',
        'ask_radiation_image',
        'placeRadiation',
        'ask_magnetic_image',
        'place_magnetic',
        'ask_operationAction',
        'NameActionOperation',
        'drugs_id',
        'result_examination',
        'medical_recomendation'
    ];


    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    protected $casts=[
        'previous_illnesses'=>'array',
        'Current_symptoms'=>'array',
        'Medications_taken'=>'array',
        'id_medical_examination'=>'array',
        'drugs_id'=>'array',
        'laboratory_analysis'=>'array'
    ];
}
