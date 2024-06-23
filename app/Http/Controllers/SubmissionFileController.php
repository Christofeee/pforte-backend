<?php

namespace App\Http\Controllers;

use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Exception;

class SubmissionFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get all submissions
        $submissionData = Submission::all();
        return response()->json($submissionData, 200);
    }

    public function getByStudentAndAssessment(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'assessment_id' => 'required|integer',
                'student_id' => 'required|integer',
            ]);

            // Retrieve the files
            $assessmentId = $request->input('assessment_id');
            $studentId = $request->input('student_id');

            $files = SubmissionFile::where('assessment_id', $assessmentId)
                ->where('student_id', $studentId)
                ->get();

            if ($files->isEmpty()) {
                return response()->json(['error' => 'No files found for the given assessment and student.'], 404);
            }

            return response()->json(['files' => $files], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve files: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function upload(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'files.*' => 'required|file|max:10240',  // Accept any file type
                'assessment_id' => 'required|integer',
                'student_id' => 'required|integer',
            ]);

            // Check if files are present in the request
            if ($request->hasFile('files')) {
                $assessmentId = $request->input('assessment_id');
                $studentId = $request->input('student_id');
                $uploadedFiles = [];

                foreach ($request->file('files') as $file) {
                    $originalName = preg_replace('/[^a-zA-Z0-9\-\._]/', '_', $file->getClientOriginalName());

                    // Check if a file with the same name, assessment_id, and student_id already exists
                    $existingFile = SubmissionFile::where('file_name', $originalName)
                        ->where('assessment_id', $assessmentId)
                        ->where('student_id', $studentId)
                        ->first();
                    if ($existingFile) {
                        return response()->json(['error' => 'The file "' . $originalName . '" already exists for this assessment and student.'], 400);
                    }

                    // Generate a unique filename
                    $uniqueFilename = $file->hashName();

                    // Store the file
                    $storedPath = $file->storeAs('public/submission_files', $uniqueFilename);

                    // Save the file information along with assessment_id and student_id to the database
                    $submissionFile = new SubmissionFile();
                    $submissionFile->file_name = $originalName;
                    $submissionFile->file_path = $storedPath;
                    $submissionFile->assessment_id = $assessmentId;
                    $submissionFile->student_id = $studentId;
                    $submissionFile->save();

                    $uploadedFiles[] = $submissionFile;
                }

                return response()->json(['success' => 'Files uploaded successfully', 'files' => $uploadedFiles], 200);
            } else {
                return response()->json(['error' => 'No files found in request'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'File upload failed: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
