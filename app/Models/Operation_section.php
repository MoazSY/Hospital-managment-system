<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation_section extends Model
{
    use HasFactory;
    protected $table='operation_sections';
    protected $fillable=[
        'Section_name',
        'doctors_id',
        'info_section',
        'contact_info'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }


    public function operation_rooms()
    {
        return $this->hasMany(Operation_rooms::class);
    }
}

