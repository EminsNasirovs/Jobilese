<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvDetail extends Model
{
   protected $casts = [
    'experience' => 'array',
    'education' => 'array',
    'skills' => 'array',
];
}
