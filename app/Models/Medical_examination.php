<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical_examination extends Model
{
    use HasFactory;
    protected $table='medical_examination';
    protected $fillable=[
        'name_examination',
        'info_examination'
    ];
}
