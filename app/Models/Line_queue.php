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
        'num_char'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
