<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'due_date_time', 'instruction', 'link', 'mark', 'module_id', 'classroom_id'];
}
