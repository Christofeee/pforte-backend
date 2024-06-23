<?php

namespace App\Http\Controllers;

use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ZipArchive;
use Illuminate\Support\Facades\Log;

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
        $submissionData = SubmissionFile::all();
        return response()->json($submissionData, 200);
    }

    public function getByStudentAndAssessment(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'assessment_id' => 'required|integer',
                'student_id' => 'required|string',
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

    public function downloadFileByStudentAndAssessment(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'assessment_id' => 'required|integer',
                'student_id' => 'required|string',
            ]);

            // Retrieve the files
            $assessmentId = $request->input('assessment_id');
            $studentId = $request->input('student_id');

            // Retrieve files from database
            $files = SubmissionFile::where('assessment_id', $assessmentId)
                ->where('student_id', $studentId)
                ->get();

            if ($files->isEmpty()) {
                return response()->json(['error' => 'No files found for the given assessment and student.'], 404);
            }

            // Create a ZIP file
            $zip = new ZipArchive;
            $zipFileName = 'files_' . $assessmentId . '_' . $studentId . '.zip';
            $zipFilePath = storage_path('app/public/' . $zipFileName);

            Log::info('Creating ZIP file at path: ' . $zipFilePath);

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($files as $file) {
                    // Adjusted file path handling
                    $filePath = storage_path('app/' . $file->file_path);

                    if (file_exists($filePath)) {
                        Log::info('Adding file to ZIP: ' . $filePath);
                        $zip->addFile($filePath, basename($filePath)); // Ensure to use correct parameters
                    } else {
                        Log::warning('File does not exist: ' . $filePath);
                    }
                }

                $zip->close();
                Log::info('ZIP file created successfully.');

                // Verify that the ZIP file exists before attempting to download
                if (file_exists($zipFilePath)) {
                    Log::info('ZIP file exists, preparing download.');
                    return response()->download($zipFilePath)->deleteFileAfterSend(true);
                } else {
                    Log::error('ZIP file does not exist: ' . $zipFilePath);
                    return response()->json(['error' => 'The file "' . $zipFilePath . '" does not exist'], 500);
                }
            } else {
                Log::error('Failed to create ZIP file.');
                return response()->json(['error' => 'Failed to create ZIP file'], 500);
            }

        } catch (Exception $e) {
            Log::error('Exception occurred: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to download files: ' . $e->getMessage()], 500);
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
