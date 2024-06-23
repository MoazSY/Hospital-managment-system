<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Drugs_supplies extends Model
{
    use HasFactory;
    protected $table='drugs_supplies';
    protected $fillable=[
        'pharmatical_warehouse_id',
        'name',
        'quentity',
        'category',
        'price',
        'manufacture_company'
    ];
    public function warehouse()
    {
        return $this->belongsTo(Pharmatical_warehouse::class, 'pharmatical_warehouse_id');
    }
    public function request_medicals()
    {
        return $this->hasMany(Request_medical_supplies::class);
    }
    public function consumers()
    {
        return $this->hasMany(Consumers::class);
    }
}




