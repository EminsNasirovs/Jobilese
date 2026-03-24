<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'user_id',
        'cover_letter',
        'cv_path',
    ];

    public function vacancy()
    {
        return $this->belongsTo(JobVacancy::class, 'vacancy_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
