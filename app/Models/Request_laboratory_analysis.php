<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request_laboratory_analysis extends Model
{
    use HasFactory;
    protected $table='request_laboratory_analysis';
    protected $fillable=[
        'doctors_id',
        'patient_id',
        'laboratory_anylysis_id',
        'section_name',
        'section_id',
        'date',
        'status_request'
            ];
}
