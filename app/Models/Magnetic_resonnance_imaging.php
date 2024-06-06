<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Magnetic_resonnance_imaging extends Model
{
    use HasFactory;
    protected $table='magnetic_resonnance_imaging';
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
    public function imaging_report()
    {
        return $this->hasMany(Imaging_report::class);
    }
}

