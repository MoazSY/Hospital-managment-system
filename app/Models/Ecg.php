<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ecg extends Model
{
    use HasFactory;
    protected $table='ecg';
    protected $fillable=[
        'medical_examination_id',
        'patient_id',
        'doctors_id',
        'date',
        'image_Ecg',
        'result_Ecg'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }


}

