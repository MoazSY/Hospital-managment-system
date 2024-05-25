<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory_anylysis extends Model
{
    use HasFactory;
    protected $table='laboratory_anylysis';
    protected $fillable=[
        'name',
        'type',
        'masurement_unit',
        'natural_limit',
        'price'
    ];

    public function result_laboratory()
    {
        return $this->hasMany(Result_Laboratory_anylysis::class);
    }
}

