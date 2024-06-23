<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'submission_files';

    // Specify the primary key (if not 'id')
    protected $primaryKey = 'id';

    // Specify the fields that can be mass assigned
    protected $fillable = [
        'file_name',
        'file_path',
        'assessment_id',
        'student_id',
        'created_at',
        'updated_at',
    ];
}
