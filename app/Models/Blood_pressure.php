<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blood_pressure extends Model
{
    use HasFactory;
    protected $table='blood_pressure';
    protected $fillable=[
        'medical_examination_id',
        'patient_id',
        'id_nurse',
        'id_doctor',
        'date',
        'systolic_blood_pressure',
        'diastolic_blood_pressure',
        'number_pulses',
        'result'
    ];
    /**
     * Get the user that owns the Blood_pressure
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medical_exam()
    {
        return $this->belongsTo(Medical_examination::class, 'medical_examination_id');
    }
    /**
     * Get the user that owns the Blood_pressure
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

}
