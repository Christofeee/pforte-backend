<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pdf;
use Exception;

class PdfUploadController extends Controller
{
    public function getFileById(Request $request, $id)
    {
        try {
            $pdf = Pdf::find($id);

            if (!$pdf) {
                return response()->json(['error' => 'PDF not found'], 404);
            }

            // Construct the file path
            $filePath = storage_path('app/' . $pdf->path);

            // Check if the file exists
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'PDF file not found on the server'], 404);
            }

            // Read the file content
            $fileContent = file_get_contents($filePath);

            // Prepare headers
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $pdf->title . '"',
            ];

            // Return the file content as response
            return response($fileContent, 200)->withHeaders($headers);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve PDF: ' . $e->getMessage()], 500);
        }
    }

    public function getFilesByIds(Request $request)
    {
        try {
            $ids = $request->input('ids'); // Assuming ids are passed as an array in the request body

            // Fetch PDFs based on the provided IDs
            $pdfs = Pdf::whereIn('id', $ids)->get();

            if ($pdfs->isEmpty()) {
                return response()->json(['error' => 'PDFs not found for the provided IDs'], 404);
            }

            $pdfFiles = [];

            foreach ($pdfs as $pdf) {
                $filePath = storage_path('app/' . $pdf->path);

                if (file_exists($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    $base64Content = base64_encode($fileContent); // Base64 encode to safely transmit binary data in JSON
                    $pdfFiles[] = [
                        'id' => $pdf->id,
                        'title' => $pdf->title,
                        'content' => $base64Content,
                    ];
                }
            }

            return response()->json($pdfFiles, 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve PDFs: ' . $e->getMessage()], 500);
        }
    }



    public function upload(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'fileName' => 'required|file|mimes:pdf|max:10240', // 10 MB max file size
            ]);

            if ($request->hasFile('fileName')) {
                $file = $request->file('fileName');

                // Sanitize the original filename to remove any unsafe characters
                $originalName = preg_replace('/[^a-zA-Z0-9\-\._]/', '_', $file->getClientOriginalName());

                // Define the storage path
                $path = 'public/pdfs';

                // Store the file with its original name
                $storedPath = $file->storeAs($path, $originalName);

                // Store file info into the database
                $save = new Pdf();
                $save->title = $originalName;
                $save->path = $storedPath;
                $save->save();

                return response()->json(['success' => 'File uploaded successfully'], 200);
            } else {
                return response()->json(['error' => 'No file found in request'], 400);
            }
        } catch (Exception $e) {
            // Handle any exceptions that occur during the file upload or database saving process
            return response()->json(['error' => 'File upload failed: ' . $e->getMessage()], 500);
        }
    }
}
