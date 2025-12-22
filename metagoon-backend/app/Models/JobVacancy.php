<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'company',
        'salary',
        'description',
        'salary_type',
        'logo',
        'category',
        'county',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function applications()
{
    return $this->hasMany(JobApplication::class, 'vacancy_id');
}
}
