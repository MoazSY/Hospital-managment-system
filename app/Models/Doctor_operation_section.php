<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor_operation_section extends Model
{
    use HasFactory;
 protected $table='doctor_operation_section';
 protected $fillable=[
'doctors_id',
'operation_sections_id',
'startWorkTime',
'endWorkTime',
'days'
 ];

 public function doctor()
 {
     return $this->belongsTo(Doctor::class, 'doctors_id');
 }

 public function user()
 {
     return $this->belongsTo(Operation_section::class, 'operation_sections_id');
 }
 protected $casts=[
'days'=>'array'
 ];
}

