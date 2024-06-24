<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        'mark',
        'student_id',
        'student_name',
        'assessment_id',
        'module_id',
        'classroom_id'
    ];
}
