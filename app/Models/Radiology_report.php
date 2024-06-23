<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radiology_report extends Model
{
    use HasFactory;
    protected $table='radiology_report';
    protected $fillable=[
        'name_radiology',
        'radiation_section_id',
        'doctors_id',
        'price',
        'image',
        'medical_diagnosis',
        'patient_id'
    ];

    public function radiation_section()
    {
        return $this->belongsTo(Radiation_section::class, 'radiation_section_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'radiation_section_id');
    }
    protected $casts=['image'=>'array'];
}

