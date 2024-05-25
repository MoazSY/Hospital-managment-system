<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Nurse_section extends Model
{
    use HasFactory;
    protected $table='nurse_section';
    protected $fillable=
    [
        'nurses_id',
        'operation_sections_id',
        'startTime',
        'endTime',
        'days'
];

public function operation_section()
{
    return $this->belongsTo(Operation_section::class, 'operation_sections_id');
}

public function nurse()
{
    return $this->belongsTo(Nurse::class, 'nurses_id');
}
protected $casts=[
    'days'=>'array'
];
}

