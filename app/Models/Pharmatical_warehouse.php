<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmatical_warehouse extends Model
{
    use HasFactory;
    protected $table='pharmatical_warehouse';
    protected $fillable=[
        'warehouse_manager_id',
        'address',
        'contact_info',
        'details_info'

    ];

    public function warehouse_manager()
    {
        return $this->belongsTo(Warehouse_manager::class, 'warehouse_manager_id');
    }


    public function Drugs_supplies()
    {
        return $this->hasMany(Drugs_supplies::class);
    }
}

