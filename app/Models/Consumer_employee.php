<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\BelongsToRelationship;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Consumer_employee extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;

    protected $table='consumer_employee';
    protected $guard='consumer_employee';
    protected $fillable=[
        'name',
        'image',
        'birthdate',
        'about_him',
        'phoneNumber',
        'userName',
        'password',
        'email',
        'operation_sections_id'
    ];
    /**
     * Get the user that owns the Consumer_employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operation_section()
    {
        return $this->belongsTo(Operation_section::class, 'operation_sections_id');
    }

    public function consumers()
    {
        return $this->hasMany(Consumers::class);
    }
}
