<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pdf;
use Illuminate\Support\Facades\Log;
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

            $filePath = storage_path('app/' . $pdf->path);

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'PDF file not found on the server'], 404);
            }

            $fileContent = file_get_contents($filePath);

            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $pdf->title . '"',
            ];

            return response($fileContent, 200)->withHeaders($headers);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve PDF: ' . $e->getMessage()], 500);
        }
    }

    public function getFilesByIds(Request $request)
    {
        try {
            $ids = $request->input('ids');

            $pdfs = Pdf::whereIn('id', $ids)->get();

            if ($pdfs->isEmpty()) {
                return response()->json(['error' => 'PDFs not found for the provided IDs'], 404);
            }

            $pdfFiles = [];

            foreach ($pdfs as $pdf) {
                $filePath = storage_path('app/' . $pdf->path);

                if (file_exists($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    $base64Content = base64_encode($fileContent);
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
            $request->validate([
                'fileName' => 'required|file|mimes:pdf|max:10240',
            ]);

            if ($request->hasFile('fileName')) {
                $file = $request->file('fileName');
                $originalName = preg_replace('/[^a-zA-Z0-9\-\._]/', '_', $file->getClientOriginalName());

                // Check if a PDF with the same title already exists
                $existingPdf = Pdf::where('title', $originalName)->first();
                if ($existingPdf) {
                    return response()->json(['error' => 'A PDF with the same title already exists'], 400);
                }

                $path = 'public/pdfs';
                $storedPath = $file->storeAs($path, $originalName);

                $save = new Pdf();
                $save->title = $originalName;
                $save->path = $storedPath;
                $save->save();

                return response()->json(['success' => 'File uploaded successfully'], 200);
            } else {
                return response()->json(['error' => 'No file found in request'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'File upload failed: ' . $e->getMessage()], 500);
        }
    }
}
