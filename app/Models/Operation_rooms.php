<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation_rooms extends Model
{
    use HasFactory;
    protected $table='operation_rooms';
    protected $fillable=[
        'operation_sections_id',
        'numberRoom',
        'available',
        'hour_price_stay',
    ];

    public function operation_section()
    {
        return $this->belongsTo(Operation_section::class, 'operation_sections_id');
    }

    public function stay_room()
    {
        return $this->hasMany(Stay_operation_rooms::class);
    }
}
