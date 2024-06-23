<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit_details extends Model
{
    use HasFactory;
    protected $table='visit_details';
    protected $fillable=[
        'patient_id',
        'doctors_id',
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

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }
    protected $casts = [
        'enterTime' => 'datetime:H:i', // Adjust format as needed
    ];
}
