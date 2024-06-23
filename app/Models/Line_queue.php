<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Line_queue extends Model
{
    use HasFactory;
    protected $table='line_queue';
    protected $fillable=[
        'patient_id',
        'num_char',
        'position',
        'section_name',
        'section_id',
        'visit_id',
        'wating'
    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}

