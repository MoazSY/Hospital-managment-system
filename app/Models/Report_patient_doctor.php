<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report_patient_doctor extends Model
{
    use HasFactory;
    protected $table='report_patient_doctor';
    protected $fillable=[
 'nurses_id',
 'doctors_id',
 'patient_id',
 'operation_sections_id',
 'name_examination',
 'id_examination',
 'status_patient',
 'explanation',
 'date'
    ];

}
