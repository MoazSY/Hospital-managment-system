<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eco extends Model
{
    use HasFactory;
    protected $table='eco';
    protected $fillable=[
        'medical_examination_id',
        'patient_id',
        'doctors_id',
        'date',
        'image_eco',
        'mitral_value',
        'aortic_value',
        'tricuspid_valve',
        'pulmonary_valve',
        'left_artial_valve',
        'hijab_among_ears',
        'interventricular_diaphragm',
        'left_ventricle',
        'right_ventricle',
        'pericardium',
        'result_eco'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }
    protected $casts=[
        'image'=>'array'
    ];
}
