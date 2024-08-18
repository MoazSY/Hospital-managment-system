<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory_section extends Model
{
    use HasFactory;
    protected $table='laboratory_sections';
    protected $fillable=[
        'type_laboratory',
        'address',
        'contact_info',
        'about_him',
        'laboratorys_id',
        'start_time',
        'end_time',
        'days',
        'available'
    ];
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratorys_id');
    }
protected $casts=[
    'days'=>'array'
];
}

