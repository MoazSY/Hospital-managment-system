<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request_medical_supplies extends Model
{
    use HasFactory;
    protected $table='request_medical_supplies';
    protected $fillable=[
        'doctor_id',
        'nurse_id',
        'drugs_supplies_id',
        'quentity',
        'operation_sections_id',
        'patient_id',
        'date',
        'status_request',
        'medical_operation_id'
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function drugs_supplies()
    {
        return $this->belongsTo(Drugs_supplies::class, 'drugs_supplies_id');
    }
}


