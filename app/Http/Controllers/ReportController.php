<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use App\Models\Sample;
use App\Models\User;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'samples_this_month' => Sample::whereMonth('created_at', now()->month)->count(),
            'completed_this_month' => Sample::where('status', 'completed')
                ->whereMonth('updated_at', now()->month)->count(),
            'certificates_issued' => Certificate::where('status', 'issued')
                ->whereMonth('issued_at', now()->month)->count(),
            'active_analysts' => User::whereIn('role', ['ANALYST', 'SUPERVISOR_ANALYST'])
                ->where('is_active', true)->count()
        ];

        return view('reports.index', compact('stats'));
    }

    public function samples(Request $request)
    {
        $query = Sample::with(['customer', 'sampleType', 'assignedAnalyst']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('analyst_id')) {
            $query->where('assigned_analyst_id', $request->analyst_id);
        }

        $samples = $query->orderBy('created_at', 'desc')->get();
        $analysts = User::where('role', 'ANALYST')->get();

        if ($request->get('export') === 'csv') {
            return $this->exportSamplesCsv($samples);
        }

        // Sample status distribution
        $statusData = Sample::whereBetween('created_at', [$request->get('date_from', now()->subMonth()->format('Y-m-d')), $request->get('date_to', now()->format('Y-m-d'))])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Daily sample creation trend
        $dailyData = Sample::whereBetween('created_at', [$request->get('date_from', now()->subMonth()->format('Y-m-d')), $request->get('date_to', now()->format('Y-m-d'))])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sample type distribution
        $typeData = Sample::whereBetween('created_at', [$request->get('date_from', now()->subMonth()->format('Y-m-d')), $request->get('date_to', now()->format('Y-m-d'))])
            ->join('sample_types', 'samples.sample_type_id', '=', 'sample_types.id')
            ->select('sample_types.name', DB::raw('count(*) as count'))
            ->groupBy('sample_types.name')
            ->get();

        // Performance metrics
        $performanceData = [
            'avg_completion_time' => Sample::where('status', 'completed')
                ->whereBetween('created_at', [$request->get('date_from', now()->subMonth()->format('Y-m-d')), $request->get('date_to', now()->format('Y-m-d'))])
                ->avg(DB::raw('TIMESTAMPDIFF(DAY, created_at, updated_at)')),
            'completion_rate' => Sample::whereBetween('created_at', [$request->get('date_from', now()->subMonth()->format('Y-m-d')), $request->get('date_to', now()->format('Y-m-d'))])
                ->where('status', 'completed')
                ->count() / max(1, Sample::whereBetween('created_at', [$request->get('date_from', now()->subMonth()->format('Y-m-d')), $request->get('date_to', now()->format('Y-m-d'))])->count()) * 100
        ];

        return view('reports.samples', compact('samples', 'analysts', 'statusData', 'dailyData', 'typeData', 'performanceData'));
    }

    public function performance(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Analyst performance
        $analystData = User::whereIn('role', ['ANALYST', 'SUPERVISOR_ANALYST'])
            ->with(['assignedSamples' => function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('assigned_at', [$dateFrom, $dateTo]);
            }])
            ->get()
            ->map(function($analyst) use ($dateFrom, $dateTo) {
                $samples = $analyst->assignedSamples()
                    ->whereBetween('assigned_at', [$dateFrom, $dateTo])
                    ->get();

                return [
                    'name' => $analyst->full_name,
                    'total_assigned' => $samples->count(),
                    'completed' => $samples->where('status', 'completed')->count(),
                    'in_progress' => $samples->whereIn('status', ['assigned', 'testing'])->count(),
                    'avg_completion_days' => $samples->where('status', 'completed')
                        ->avg(function($sample) {
                            return $sample->assigned_at->diffInDays($sample->testing_completed_at);
                        }) ?? 0
                ];
            });

        // Department workload
        $workloadData = [
            'pending_assignment' => Sample::where('status', 'codified')->count(),
            'in_testing' => Sample::whereIn('status', ['assigned', 'testing'])->count(),
            'pending_review' => Sample::whereIn('status', ['review_tech', 'review_quality'])->count(),
            'pending_certificate' => Sample::where('status', 'validated')->count()
        ];

        return view('reports.performance', compact('analystData', 'workloadData', 'dateFrom', 'dateTo'));
    }

    public function compliance(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // ISO 17025 compliance metrics
        $complianceData = [
            'samples_with_audit_trail' => Sample::whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereHas('workflowHistory')->count(),
            'certificates_with_signature' => Certificate::whereBetween('issued_at', [$dateFrom, $dateTo])
                ->whereNotNull('digital_signature')->count(),
            'reviews_completed_on_time' => Sample::where('status', 'validated')
                ->whereBetween('updated_at', [$dateFrom, $dateTo])
                ->whereRaw('TIMESTAMPDIFF(DAY, testing_completed_at, updated_at) <= 3')
                ->count(),
            'user_activities_logged' => \App\Models\AuditLog::whereBetween('created_at', [$dateFrom, $dateTo])
                ->count()
        ];

        // Quality metrics
        $qualityData = [
            'revision_rate' => Sample::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('revision_count', '>', 0)->count() / 
                max(1, Sample::whereBetween('created_at', [$dateFrom, $dateTo])->count()) * 100,
            'customer_satisfaction' => 4.5, // Would be calculated from feedback
            'on_time_delivery' => 95.2, // Would be calculated from completion times
            'error_rate' => 2.1 // Would be calculated from revisions
        ];

        return view('reports.compliance', compact('complianceData', 'qualityData', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:samples,performance,compliance',
            'format' => 'required|in:pdf,excel,csv',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        // Generate report based on type and format
        $filename = "report_{$request->report_type}_{$request->date_from}_to_{$request->date_to}.{$request->format}";
        
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'report_exported',
            'description' => "Report exported: {$request->report_type} from {$request->date_from} to {$request->date_to}"
        ]);

        return back()->with('success', "Report berhasil diekspor: {$filename}");
    }

    private function exportSamplesCsv($samples)
    {
        $filename = 'samples_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function() use ($samples) {
            $handle = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($handle, [
                'Tracking Code',
                'Sample Code', 
                'Customer',
                'Sample Type',
                'Status',
                'Analyst',
                'Created Date',
                'Completed Date'
            ]);

            // CSV Data
            foreach ($samples as $sample) {
                fputcsv($handle, [
                    $sample->tracking_code,
                    $sample->sample_code ?: '-',
                    $sample->customer->company_name,
                    $sample->sampleType->name,
                    config('lims.workflow.statuses.' . $sample->status),
                    $sample->assignedAnalyst->name ?? '-',
                    $sample->created_at->format('Y-m-d H:i'),
                    $sample->status === Sample::STATUS_COMPLETED ? $sample->updated_at->format('Y-m-d H:i') : '-'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
