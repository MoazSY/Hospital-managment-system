<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient_bill extends Model
{
    use HasFactory;
    protected $table='patient_bill';
    protected $fillable=[
        'accounter_id',
        'consumers_price',
        'stay_price',
        'operation_price',
        'radiology_report_price',
        'magnitic_report_price',
        'laboratory_analysis_price',
        'doctor_examination_price',
        'patient_id',
        'total_bill'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function accounter()
    {
        return $this->belongsTo(Accounter::class, 'accounter_id');
    }
}
