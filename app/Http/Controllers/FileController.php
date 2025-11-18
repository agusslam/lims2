<?php

namespace App\Http\Controllers;

use App\Models\SampleFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function downloadSampleFile($sampleId, $fileId)
    {
        $file = SampleFile::where('sample_id', $sampleId)
                         ->where('id', $fileId)
                         ->firstOrFail();

        // Check if user has access to this sample
        $user = auth()->user();
        $sample = $file->sample;

        if (!$user->canAccessModule('all') && 
            $sample->assigned_analyst_id !== $user->id &&
            $sample->customer_id !== $user->id) {
            abort(403);
        }

        if (!Storage::exists($file->file_path)) {
            abort(404, 'File not found');
        }

        return Response::download(
            Storage::path($file->file_path),
            $file->original_name,
            ['Content-Type' => $file->mime_type]
        );
    }

    public function previewSampleFile($sampleId, $fileId)
    {
        $file = SampleFile::where('sample_id', $sampleId)
                         ->where('id', $fileId)
                         ->firstOrFail();

        if (!Storage::exists($file->file_path)) {
            abort(404, 'File not found');
        }

        if ($file->isImage() || $file->isPdf()) {
            return Response::file(
                Storage::path($file->file_path),
                ['Content-Type' => $file->mime_type]
            );
        }

        return $this->downloadSampleFile($sampleId, $fileId);
    }
}
