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
            'student_id' => 'required|string',
            'student_name' => 'required|string',
            'assessment_id' => 'required|integer',
            'isSubmitted' => 'required|boolean',
        ]);

        $submission = Submission::create([
            'student_id' => $request->student_id,
            'student_name' => $request->student_name,
            'assessment_id' => $request->assessment_id,
            'isSubmitted' => $request->isSubmitted,
        ]);

        return response()->json($submission, 201);
    }

    public function getByAssessmentId($assessment_id)
    {
        $submissions = Submission::where('assessment_id', $assessment_id)->get();

        if ($submissions->isEmpty()) {
            return response()->json(['message' => 'No submissions found for the given assessment_id'], 404);
        }

        return response()->json($submissions);
    }
}
