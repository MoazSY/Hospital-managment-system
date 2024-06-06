<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit_laboratory extends Model
{

    use HasFactory;
    protected $table='visit_laboratory';
    protected $fillable=[
        'patient_id',
        'laboratorys_id',
        'enterDate',
        'enterTime',
        'endTime',
        'typeVisit',
        'section_name',
        'section_id'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratorys_id');
    }

}
