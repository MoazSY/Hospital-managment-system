<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient_ready_accounting extends Model
{
    use HasFactory;
    protected $table='patient_ready_accounting';
    protected $fillable=[
    'patient_id',
    'consumer_employee_id',
    'accounter_id',
    'medical_operation_id',
    'consumers_id',
    'accounting'
];

}
