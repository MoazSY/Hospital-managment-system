<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result_Laboratory_anylysis extends Model
{
    use HasFactory;
    protected $table='result_laboratory_anylysis';
    protected $fillable=[
        'laboratorys_id',
        'patient_id',
        'laboratory_anylysis_id',
        'result_date',
        'result',
        'for_operations'
    ];

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratorys_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function laboratory_anylysis()
    {
        return $this->belongsTo(laboratory_anylysis::class, 'laboratory_anylysis_id');
    }

}


