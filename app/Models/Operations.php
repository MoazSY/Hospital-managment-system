<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operations extends Model
{
    use HasFactory;
    protected $table='operations';
    protected $fillable=[
        'name_operation',
        'type_operation',
        'doctors_id',
        'price_operation',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }

    public function medical_operation()
    {
        return $this->hasMany(Medical_operation::class);
    }
}
