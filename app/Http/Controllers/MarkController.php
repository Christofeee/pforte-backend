<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use Illuminate\Http\Request;

class MarkController extends Controller
{
    // Fetch all marks
    public function getAllMarks()
    {
        $marks = Mark::all();
        return response()->json($marks);
    }

    // Fetch marks by student_id and assessment_id
    public function getMarksByStudentAndAssessment($student_id, $assessment_id)
    {
        $marks = Mark::where('student_id', $student_id)
            ->where('assessment_id', $assessment_id)
            ->get();
        return response()->json($marks);
    }

    // Fetch marks by assessment_id
    public function getMarksByAssessment($assessment_id)
    {
        $marks = Mark::where('assessment_id', $assessment_id)->get();
        return response()->json($marks);
    }

    // Fetch marks by classroom_id
    public function getMarksByClassroom($classroom_id)
    {
        $marks = Mark::where('classroom_id', $classroom_id)->get();
        return response()->json($marks);
    }

    // Fetch marks by module_id
    public function getMarksByModule($module_id)
    {
        $marks = Mark::where('module_id', $module_id)->get();
        return response()->json($marks);
    }

    // Fetch marks by assessment_id and multiple student_ids
    public function getMarksByAssessmentAndStudents($assessment_id, Request $request)
    {
        $student_ids = $request->input('student_ids');
        $marks = Mark::where('assessment_id', $assessment_id)
            ->whereIn('student_id', $student_ids)
            ->get();
        return response()->json($marks);
    }

    // Save or update marks
    // Save or update marks
    public function saveMarks(Request $request)
    {
        $marksData = $request->json()->all();

        foreach ($marksData as $data) {
            $existingMark = Mark::where('student_id', $data['student_id'])
                ->where('assessment_id', $data['assessment_id'])
                ->first();

            if ($existingMark) {
                // Update existing mark
                $existingMark->update([
                    'mark' => $data['mark'],
                    'student_name' => $data['student_name'],
                    'module_id' => $data['module_id'],
                    'classroom_id' => $data['classroom_id'],
                ]);
            } else {
                // Create new mark
                Mark::create([
                    'mark' => $data['mark'],
                    'student_id' => $data['student_id'],
                    'student_name' => $data['student_name'],
                    'assessment_id' => $data['assessment_id'],
                    'module_id' => $data['module_id'],
                    'classroom_id' => $data['classroom_id'],
                ]);
            }
        }

        return response()->json(['message' => 'Marks saved successfully']);
    }
}
