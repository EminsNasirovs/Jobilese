<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    protected $table = 'job_comment'; 

    protected $fillable = [
        'vacancy_id',
        'user_id',
        'comment_text',
        'parent_id',
    ];

    public function vacancy()
    {
        return $this->belongsTo(JobVacancy::class, 'vacancy_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(Comments::class, 'parent_id')->with('user:id,username');
    }
}
