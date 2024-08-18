<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical_operation extends Model
{
    use HasFactory;
    protected $table='medical_operation';
    protected $fillable=[
        'operations_id',
        'patient_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'status_operation',
        'recomendation',
        'id_drugs',
        'doctors_id'
    ];

    public function operation()
    {
        return $this->belongsTo(Operations::class, 'operations_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    protected $casts=[
        'id_drugs'=>'array'
    ];
}
