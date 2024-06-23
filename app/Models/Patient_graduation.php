<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient_graduation extends Model
{
    use HasFactory;
    protected $table='patient_graduation';
    protected $fillable=[
        'doctors_id',
        'patient_id',
        'out_date',
        'out_time',
        'recomendation'
    ];
}
