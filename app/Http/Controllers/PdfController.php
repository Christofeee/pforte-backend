<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;

class PdfController extends Controller
{
    public function getPdfFilesByModuleId($moduleId)
    {
        try {
            $pdfs = Pdf::where('moduleId', $moduleId)->get();

            if ($pdfs->isEmpty()) {
                return response()->json(['error' => 'PDFs not found for the provided moduleId'], 404);
            }

            $pdfFiles = [];

            foreach ($pdfs as $pdf) {
                $pdfFiles[] = [
                    'id' => $pdf->id,
                    'title' => $pdf->title,
                    'path' => Storage::url($pdf->path), // Assuming your PDFs are stored in 'storage/app/public/pdfs'
                ];
            }

            return response()->json($pdfFiles, 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve PDFs: ' . $e->getMessage()], 500);
        }
    }

    // public function downloadPdf($pdfId)
    // {
    //     try {
    //         $pdf = Pdf::findOrFail($pdfId);
    //         Log::info($pdf->path);
    //         $filePath = 'pdfs/' . $pdf->path;

    //         if (Storage::exists($filePath)) {
    //             return Storage::download($filePath, $pdf->title);
    //         }

    //         return response()->json(['error' => 'PDF file not found'], 404);

    //     } catch (ModelNotFoundException $e) {
    //         return response()->json(['error' => 'PDF not found'], 404);
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'Failed to download PDF: ' . $e->getMessage()], 500);
    //     }
    // }

    public function downloadPdf($pdfId)
    {
        try {
            $pdf = Pdf::findOrFail($pdfId);
            Log::info($pdf->path);
            $filePath = $pdf->path; // Use the stored relative path

            if (Storage::exists($filePath)) {
                return Storage::download($filePath, $pdf->title);
            }

            return response()->json(['error' => 'PDF file not found'], 404);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'PDF not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to download PDF: ' . $e->getMessage()], 500);
        }
    }

    // public function upload(Request $request)
    // {
    //     try {
    //         // Validate the incoming request
    //         $request->validate([
    //             'fileName' => 'required|file|mimes:pdf|max:10240',
    //             'moduleId' => 'required|integer',
    //         ]);

    //         // Check if the file is present in the request
    //         if ($request->hasFile('fileName')) {
    //             $file = $request->file('fileName');
    //             $originalName = preg_replace('/[^a-zA-Z0-9\-\._]/', '_', $file->getClientOriginalName());
    //             $moduleId = $request->input('moduleId');

    //             // Check if a PDF with the same title and moduleId already exists
    //             $existingPdf = Pdf::where('title', $originalName)
    //                 ->where('moduleId', $moduleId)
    //                 ->first();
    //             if ($existingPdf) {
    //                 return response()->json(['error' => 'This PDF file already exists in this module.'], 400);
    //             }

    //             // Generate a unique filename
    //             $uniqueFilename = $file->hashName();

    //             // Store the file
    //             $storedPath = $file->storeAs('public/pdfs', $uniqueFilename);

    //             // Save the file information along with moduleId to the database
    //             $pdf = new Pdf();
    //             $pdf->title = $originalName;
    //             // Store only the relative path
    //             $pdf->path = 'pdfs/' . $uniqueFilename;
    //             $pdf->moduleId = $moduleId;
    //             $pdf->save();

    //             return response()->json(['success' => 'File uploaded successfully'], 200);
    //         } else {
    //             return response()->json(['error' => 'No file found in request'], 400);
    //         }
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'File upload failed: ' . $e->getMessage()], 500);
    //     }
    // }

    public function upload(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'fileName' => 'required|file|mimes:pdf|max:10240',
                'moduleId' => 'required|integer',
            ]);

            // Check if the file is present in the request
            if ($request->hasFile('fileName')) {
                $file = $request->file('fileName');
                $originalName = preg_replace('/[^a-zA-Z0-9\-\._]/', '_', $file->getClientOriginalName());
                $moduleId = $request->input('moduleId');

                // Check if a PDF with the same title and moduleId already exists
                $existingPdf = Pdf::where('title', $originalName)
                    ->where('moduleId', $moduleId)
                    ->first();
                if ($existingPdf) {
                    return response()->json(['error' => 'This PDF file already exists in this module.'], 400);
                }

                // Generate a unique filename
                $uniqueFilename = $file->hashName();

                // Store the file
                $storedPath = $file->storeAs('public/pdfs', $uniqueFilename);

                // Save the file information along with moduleId to the database
                $pdf = new Pdf();
                $pdf->title = $originalName;
                $pdf->path = $storedPath;
                $pdf->moduleId = $moduleId;
                $pdf->save();

                return response()->json(['success' => 'File uploaded successfully'], 200);
            } else {
                return response()->json(['error' => 'No file found in request'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'File upload failed: ' . $e->getMessage()], 500);
        }
    }
}
