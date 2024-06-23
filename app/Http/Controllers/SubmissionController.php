<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    // Get all submissions
    public function index()
    {
        $submissions = Submission::all();
        return response()->json($submissions);
    }

    // Create a new submission
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'assessment_id' => 'required|integer',
            'isSubmitted' => 'required|boolean',
        ]);

        $submission = Submission::create([
            'student_id' => $request->student_id,
            'assessment_id' => $request->assessment_id,
            'isSubmitted' => $request->isSubmitted,
        ]);

        return response()->json($submission, 201);
    }
}
