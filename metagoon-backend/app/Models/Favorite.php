<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'job_vacancy_id'];

    public function vacancy() {
        return $this->belongsTo(\App\Models\JobVacancy::class, 'job_vacancy_id');
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}
