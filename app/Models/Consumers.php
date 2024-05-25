<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumers extends Model
{
    use HasFactory;
    protected $table='consumers';
    protected $fillable=[
        'patient_id',
        'drugs_supplies_id',
        'quentity',
        'consumer_employee_id',
        'address'
    ];
    /**
     * Get the user that owns the Consumers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    /**
     * Get the user that owns the Consumers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function drugs()
    {
        return $this->belongsTo(Drugs_supplies::class, 'drugs_supplies_id');
    }
}
