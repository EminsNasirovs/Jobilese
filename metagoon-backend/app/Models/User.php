<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'gender',
        'birth_date',
        'role',
        'company_name',
        'company_number',
        'company_address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function jobVacancies()
{
    return $this->hasMany(JobVacancy::class);
}
}
