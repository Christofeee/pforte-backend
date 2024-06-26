<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassroomUser extends Model
{
    use HasFactory;

    protected $fillable = ['classroom_id', 'user_id'];
    protected $primaryKey = 'classroom_user_id';

}
