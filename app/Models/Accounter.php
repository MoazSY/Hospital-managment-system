<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\BelongsToRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;
use App\Models\Operation_section;
class Accounter extends Authenticatable
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $table='accounter';
    protected $guard='accounter';
    protected $fillable=[
        'name',
         'image',
         'birthdate',
        'about_him',
        'phoneNumber',
        'userName',
        'password',
        'email',
        'operation_sections_id',
        'address'
    ];

    public function operation_section(){
        return $this->belongsTo(Operation_section::class,'operation_sections_id');
    }

public function bills()
{
    return $this->hasMany(Patient_bill::class);
}

}
