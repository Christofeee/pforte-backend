<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentFile;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    public function index()
    {
        //get all classrooms
        $assessments = Assessment::get();
        return response()->json($assessments, 200);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'due_date_time' => 'required|date',
                'instruction' => 'nullable|string',
                'link' => 'nullable|string|max:1000',
                'mark' => 'nullable|integer',
                'module_id' => 'nullable|integer',
                'classroom_id' => 'nullable|integer',
                'files' => 'nullable|array',
                'files.*' => 'required_with:files|file|max:10240',
            ]);

            Log::info("validation passed in assessment store function");

            $assessment = Assessment::create($data);
            Log::info("Assessment created: " . $assessment);

            if ($request->has('files')) {
                Log::info("request has files.");
                foreach ($request->file('files') as $file) {
                    $originalName = preg_replace('/[^a-zA-Z0-9\-\._]/', '_', $file->getClientOriginalName());
                    $uniqueFilename = $file->hashName();
                    $storedPath = $file->storeAs('public/assessments/files', $uniqueFilename);

                    $assessmentFile = AssessmentFile::create([
                        'file_name' => $originalName,
                        'file_path' => $storedPath,
                        'assessment_id' => $assessment->id,
                    ]);
                    Log::info("AssessmentFile created: " . $assessmentFile);
                }
            }

            return response()->json("Assessment created successfully.", 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create assessment: ' . $e->getMessage()], 500);
        }
    }

    public function getByModuleId($moduleId)
    {
        try {
            $assessments = Assessment::where('module_id', $moduleId)->get();

            $assessmentsWithFiles = [];

            foreach ($assessments as $assessment) {
                $assessmentData = $assessment->toArray();
                $files = AssessmentFile::where('assessment_id', $assessment->id)->get();
                $assessmentData['files'] = $files;
                $assessmentsWithFiles[] = $assessmentData;
            }

            return response()->json($assessmentsWithFiles, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch assessments: ' . $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            // Find the assessment by ID
            $assessment = Assessment::findOrFail($id);

            // Get associated files
            $files = AssessmentFile::where('assessment_id', $assessment->id)->get();

            // Delete associated files from storage
            foreach ($files as $file) {
                Storage::delete($file->file_path);
                $file->delete(); // Delete file record from database
            }

            // Delete the assessment
            $assessment->delete();

            return response()->json("Assessment and its files deleted successfully.", 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete assessment: ' . $e->getMessage()], 500);
        }
    }
}
