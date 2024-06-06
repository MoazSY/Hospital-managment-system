<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radiation_section extends Model
{
    use HasFactory;
    protected $table='radiation_section';
    protected $fillable=[
        'doctors_id',
        'address',
        'contact_info',
        'info_about',
        'name',
        'available'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }

    public function Radiology_report()
    {
        return $this->hasMany(Radiology_report::class);
    }
}

