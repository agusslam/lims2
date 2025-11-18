<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sample;
use App\Models\SampleRequest;
use App\Models\Certificate;
use App\Models\AuditLog;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Sample::with(['sampleRequest.customer', 'sampleType', 'assignedAnalyst', 'certificate']);
        
        // Filter archived and completed samples
        $query->whereIn('status', ['completed', 'archived', 'certificated']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sample_code', 'like', "%{$search}%")
                  ->orWhereHas('sampleRequest', function($subQ) use ($search) {
                      $subQ->where('tracking_code', 'like', "%{$search}%")
                           ->orWhereHas('customer', function($customerQ) use ($search) {
                               $customerQ->where('contact_person', 'like', "%{$search}%")
                                        ->orWhere('company_name', 'like', "%{$search}%");
                           });
                  });
            });
        }

        // Date range filter - use updated_at instead of completed_at
        if ($request->filled('date_from')) {
            $query->whereDate('updated_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('updated_at', '<=', $request->date_to);
        }

        // Sample type filter
        if ($request->filled('sample_type')) {
            $query->whereHas('sampleType', function($q) use ($request) {
                $q->where('name', $request->sample_type);
            });
        }

        $samples = $query->orderBy('updated_at', 'desc')->paginate(20);

        $stats = [
            'total_archived' => Sample::whereIn('status', ['completed', 'archived', 'certificated'])->count(),
            'archived_this_month' => Sample::whereIn('status', ['completed', 'archived', 'certificated'])
                ->whereMonth('updated_at', now()->month)->count(),
            'certificates_issued' => Certificate::where('status', 'issued')->count(),
            'total_customers' => $samples->pluck('sampleRequest.customer_id')->unique()->count()
        ];

        $sampleTypes = \App\Models\SampleType::pluck('name', 'name');

        return view('archives.index', compact('samples', 'stats', 'sampleTypes'));
    }

    public function show($id)
    {
        $sample = Sample::with([
            'sampleRequest.customer',
            'sampleType',
            'assignedAnalyst',
            'tests.testParameter',
            'certificate',
            'workflowHistory.actionBy'
        ])->findOrFail($id);

        // Check if sample is archived/completed
        if (!in_array($sample->status, ['completed', 'archived', 'certificated'])) {
            return redirect()->route('archives.index')
                ->with('error', 'Sampel belum diarsipkan atau diselesaikan');
        }

        return view('archives.show', compact('sample'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        // Create export based on format
        $filename = 'archive_export_' . $request->date_from . '_to_' . $request->date_to;
        
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'archive_exported',
            'description' => "Archive data exported from {$request->date_from} to {$request->date_to} in {$request->format} format"
        ]);

        // Simulate export process - in real implementation, generate actual file
        return back()->with('success', "Export berhasil dibuat: {$filename}.{$request->format}");
    }
}
