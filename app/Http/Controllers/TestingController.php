<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Models\SampleTestParameter;
use App\Models\SampleFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SampleTest;
use App\Models\AuditLog;
// use App\Models\SampleResult;

class TestingController extends Controller
{
    /**
     * Display assigned samples for testing
     */
    public function index()
    {
        $query = Sample::with(['sampleType', 'parameters', 'assignedAnalyst'])
            ->where('status', 'assigned');

        // Kalau login sebagai ANALYST, hanya lihat sampel yang ditugaskan ke dirinya
        if (Auth::user()->role === 'ANALYST') {
            $query->where('assigned_analyst_id', Auth::id());
        }

        $samples = $query
            ->orderBy('assigned_at', 'desc')
            ->paginate(20);

        return view('testing.index', compact('samples'));
    }

    /**
     * Show sample for testing
     */
    public function show($id)
    {
        $sample = Sample::with([
            'sampleType', 
            'parameters', 
            'tests',
            'assignedAnalyst',
            'sampleRequest.customer',
        ])->findOrFail($id);

        // Check if user has access to this sample
        if (Auth::user()->role === 'ANALYST' && 
            $sample->assignment->analyst_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this sample');
        }

        return view('testing.show', compact('sample'));
    }

    /**
     * Start testing process
     */
    public function startTesting($id)
    {
        $sample = Sample::findOrFail($id);
        
        // Check authorization
        if (Auth::user()->role === 'ANALYST' && 
            $sample->assignment->analyst_id !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($sample) {
            $sample->update([
                'status' => 'testing',
                'testing_started_at' => now(),
                'testing_started_by' => Auth::id()
            ]);
            
            // Log audit trail
            $this->logAudit('testing_started', $sample->id, [
                'started_by' => Auth::user()->name
            ]);
        });

        return redirect()->route('testing.show', $id)
            ->with('success', 'Pengujian dimulai');
    }

    /**
     * Save test results
     */
    public function saveResults(Request $request, $parameterId)
    {
        $sampleTest = SampleTest::where('parameters_id', $parameterId)->firstOrFail();
        $sample = $sampleTest->sample;
        
        // Check authorization
        if (Auth::user()->role === 'ANALYST' && 
            $sample->assignment->analyst_id !== Auth::id()) {
            abort(403);
        }

        // $validated = $request->validate([
        //     'results' => 'required|array',
        //     'results.*' => 'required|array', 
        //     'results.*.parameter_id' => 'required|exists:parameters,id',
        //     'results.*.result_value' => 'required|string',
        //     'results.*.unit' => 'required|string',
        //     'results.*.method' => 'nullable|string',
        //     'results.*.notes' => 'nullable|string',
        //     'testing_notes' => 'nullable|string|max:2000'
        // ]);

        $validated = $request->validate([
        'result_value' => 'required|string',
        'notes'        => 'nullable|string',
        'instrument_files.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx',
    ]);

        DB::transaction(function () use ($sample, $sampleTest, $validated, $request, $parameterId) {
    // SIMPAN HASIL DI TABEL sample_tests
    $sampleTest->update([
        'result_value' => $validated['result_value'],
        'notes'        => $validated['notes'] ?? null,
        'tested_by'    => Auth::id(),
        'tested_at'    => now(),
        'status'       => 'completed',
    ]);

    // === SIMPAN FILE INSTRUMEN (kalau tabel punya kolom JSON 'instrument_files') ===
    if ($request->hasFile('instrument_files')) {
        $uploaded = [];

        foreach ($request->file('instrument_files') as $file) {
            if (!$file) continue;

            $path = $file->store(
                'uploads/samples/' . $sample->sample_code,
                'public'
            );

            $uploaded[] = [
                'filename'    => $file->getClientOriginalName(),
                'path'        => $path,
                'size'        => $file->getSize(),
                'mime'        => $file->getClientMimeType(),
                'uploaded_at' => now()->toDateTimeString(),
            ];
        }

        if ($uploaded) {
            // pastikan di model SampleTest ada:
            // protected $casts = ['instrument_files' => 'array'];
            $existing = $sampleTest->instrument_files ?? [];
            $sampleTest->instrument_files = array_merge($existing, $uploaded);
            $sampleTest->save();
        }
    }

    // AUDIT LOG
    $this->logAudit('results_saved', $sample->id, [
        'tested_by'    => Auth::user()->name,
        'parameter_id' => $parameterId,
    ]); 
});

    return back()->with('success', 'Hasil pengujian berhasil disimpan');
    }

    /**
     * Upload test files
     */
    public function uploadFiles(Request $request, $id)
    {
        $sample = Sample::findOrFail($id);
        
        // Check authorization
        if (Auth::user()->role === 'ANALYST' && 
            $sample->assignment->analyst_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx',
            'file_descriptions' => 'array',
            'file_descriptions.*' => 'nullable|string|max:255'
        ]);

        $uploadedFiles = [];
        
        DB::transaction(function () use ($sample, $validated, &$uploadedFiles) {
            foreach ($validated['files'] as $index => $file) {
                $path = $file->store('uploads/samples/' . $sample->sample_code, 'public');
                $description = $validated['file_descriptions'][$index] ?? null;
                
                $sampleFile = SampleFile::create([
                    'sample_id' => $sample->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'description' => $description,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => now()
                ]);
                
                $uploadedFiles[] = $sampleFile;
            }
            
            // Log audit trail
            $this->logAudit('files_uploaded', $sample->id, [
                'uploaded_by' => Auth::user()->name,
                'files_count' => count($validated['files'])
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diupload',
            'files' => $uploadedFiles
        ]);
    }

    /**
     * Complete testing and submit for review
     */
    public function completeTesting($id)
    {
        $sample = Sample::with(['tests', 'parameters'])->findOrFail($id);
        
        // Check authorization
        if (Auth::user()->role === 'ANALYST' && 
            $sample->assignment->analyst_id !== Auth::id()) {
            abort(403);
        }

        // Validate all parameters have results
        $missingResults = $sample->parameters->filter(function ($parameter) use ($sample) {
        return !$sample->tests
            ->where('parameters_id', $parameter->id)  // kolom yg benar di sample_tests
            ->whereNotNull('result_value')
            ->count();
    });

        if ($missingResults->count() > 0) {
        return response()->json([
            'success' => false,
            'message' => 'Semua parameter harus memiliki hasil pengujian',
        ], 422);
    }

        DB::transaction(function () use ($sample) {
            $sample->update([
                'status' => 'review_tech',
                'testing_completed_at' => now(),
                'testing_completed_by' => Auth::id()
            ]);
            
            // Log audit trail
            $this->logAudit('testing_completed', $sample->id, [
                'completed_by' => Auth::user()->name
            ]);
        });

        return response()->json([
        'success' => true,
        'message' => 'Pengujian selesai dan dikirim untuk review teknis',
    ]);
    }

    /**
     * Preview testing form
     */
    public function previewForm($id)
    {
        $sample = Sample::with([
            'sampleType', 
            'parameters', 
            'results.parameter',
            'assignment.analyst'
        ])->findOrFail($id);
        
        return view('testing.preview-form', compact('sample'));
    }

    /**
     * Print testing form
     */
    public function printForm($id)
    {
        $sample = Sample::with([
            'sampleType', 
            'parameters', 
            'results.parameter',
            'assignment.analyst'
        ])->findOrFail($id);
        
        return view('testing.print-form', compact('sample'));
    }

    /**
     * Delete uploaded file
     */
    public function deleteFile($id, $fileId)
    {
        $sample = Sample::findOrFail($id);
        $file = SampleFile::where('sample_id', $id)->findOrFail($fileId);
        
        // Check authorization
        if (Auth::user()->role === 'ANALYST' && 
            $sample->assignment->analyst_id !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($file) {
            // Delete physical file
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            
            // Delete record
            $file->delete();
            
            // Log audit trail
            $this->logAudit('file_deleted', $file->sample_id, [
                'deleted_by' => Auth::user()->name,
                'file_name' => $file->file_name
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'File berhasil dihapus'
        ]);
    }

    /**
     * Log audit trail
     */
    private function logAudit($action, $sampleId, $details = [])
    {
        DB::table('audit_logs')->insert([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => 'samples',
            'record_id' => $sampleId,
            'old_values' => null,
            'new_values' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }
}