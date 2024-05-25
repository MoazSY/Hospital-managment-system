<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay_operation_rooms extends Model
{
    use HasFactory;
    protected $table='stay_operation_rooms';
    protected $fillable=[
        'patient_id',
        'operation_rooms_id',
        'enter_time',
        'out_time',
        'enter_date',
        'out_date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function operation_room()
    {
        return $this->belongsTo(Operation_rooms::class, 'operation_rooms_id');
    }
}

