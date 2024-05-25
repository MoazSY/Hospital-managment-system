<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sugar_blood extends Model
{
    use HasFactory;
    protected $table='sugar_blood';
    protected $fillable=[
        'medical_examination_id',
        'patient_id',
        'id_nurse',
        'id_doctor',
        'date',
        'blood_sugar',
        'result'
    ];

    public function medical_examination()
    {
        return $this->belongsTo(Medical_examination::class, 'medical_examination_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}

