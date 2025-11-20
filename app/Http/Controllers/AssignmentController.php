<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Sample;
use App\Models\User;
use App\Models\SampleAssignment;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display samples ready for assignment
     */
    public function index()
    {
        $samples = Sample::with(['sampleType', 'parameters', 'codifiedBy'])
            ->where('status', 'codified')
            ->orderBy('codified_at', 'desc')
            ->paginate(20);

        $analysts = User::where('role', 'ANALYST')
            ->orWhere('role', 'SUPERVISOR_ANALYST')
            ->where('is_active', true)
            ->get();

        return view('assignments.index', compact('samples', 'analysts'));
    }

    /**
     * Show assignment form
     */
    public function show($id)
    {
        $sample = Sample::with(['sampleType', 'parameters'])->findOrFail($id);
        
        // Get available analysts based on specialization
        $analysts = $this->getAvailableAnalysts($sample);
        $workloadData = $this->getAnalystWorkload();

        return view('assignments.show', compact('sample', 'analysts', 'workloadData'));
    }

    /**
     * Auto-assign sample to analyst
     */
    public function autoAssign($id)
    {
        $sample = Sample::with(['parameters'])->findOrFail($id);
        
        DB::transaction(function () use ($sample) {
            // Get best analyst based on specialization and workload
            $analyst = $this->getBestAnalyst($sample);
            
            if (!$analyst) {
                throw new \Exception('Tidak ada analis yang tersedia untuk parameter ini');
            }

            // Create assignment
            SampleAssignment::create([
                'sample_id' => $sample->id,
                'analyst_id' => $analyst->id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'status' => 'assigned'
            ]);

            // Update sample status
            $sample->update([
                'status' => 'assigned',
                'assigned_at' => now()
            ]);
            
            // Log audit trail
            $this->logAudit('sample_auto_assigned', $sample->id, [
                'assigned_to' => $analyst->name,
                'assigned_by' => Auth::user()->name
            ]);
        });

        return redirect()->route('assignments.index')
            ->with('success', 'Sampel berhasil ditugaskan secara otomatis');
    }

    /**
     * Manual assign sample to analyst
     */
    public function assign(Request $request, $id)
    {
        $sample = Sample::findOrFail($id);
        
        $validated = $request->validate([
            'analyst_id' => 'required|exists:users,id',
            'priority' => 'required|in:low,normal,high,urgent',
            'deadline' => 'required|date|after:today',
            'assignment_notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($sample, $validated) {
            // Create assignment
            SampleAssignment::create([
                'sample_id' => $sample->id,
                'analyst_id' => $validated['analyst_id'],
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'priority' => $validated['priority'],
                'deadline' => $validated['deadline'],
                'assignment_notes' => $validated['assignment_notes'],
                'status' => 'assigned'
            ]);

            // Update sample status
            $sample->update([
                'status'              => 'assigned',
            'assigned_analyst_id' => $validated['analyst_id'],
            'assigned_to' => $validated['analyst_id'],
            'assigned_by'         => Auth::id(),
            'assigned_at'         => now(),
            'deadline'            => $validated['deadline'],
            'assignment_notes'    => $validated['assignment_notes'] ?? null,
            ]);
            
            $analyst = User::find($validated['analyst_id']);
            
            // Log audit trail
            $this->logAudit('sample_manual_assigned', $sample->id, [
                'assigned_to' => $analyst->name,
                'assigned_by' => Auth::user()->name,
                'priority' => $validated['priority'],
                'deadline' => $validated['deadline']
            ]);
        });

        return redirect()->route('assignments.index')
            ->with('success', 'Sampel berhasil ditugaskan kepada analis');
    }

    /**
     * Reassign sample to different analyst
     */
    public function reassign(Request $request, $id)
    {
        $sample = Sample::findOrFail($id);
        
        $validated = $request->validate([
            'new_analyst_id' => 'required|exists:users,id|different:current_analyst_id',
            'reassignment_reason' => 'required|string|max:500'
        ]);

        DB::transaction(function () use ($sample, $validated) {
            // Update current assignment
            $currentAssignment = SampleAssignment::where('sample_id', $sample->id)
                ->where('status', 'assigned')
                ->first();
                
            if ($currentAssignment) {
                $currentAssignment->update([
                    'status' => 'reassigned',
                    'reassignment_reason' => $validated['reassignment_reason'],
                    'reassigned_at' => now()
                ]);
            }

            // Create new assignment
            SampleAssignment::create([
                'sample_id' => $sample->id,
                'analyst_id' => $validated['new_analyst_id'],
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'status' => 'assigned',
                'reassignment_reason' => $validated['reassignment_reason']
            ]);
            
            $newAnalyst = User::find($validated['new_analyst_id']);
            
            // Log audit trail
            $this->logAudit('sample_reassigned', $sample->id, [
                'reassigned_to' => $newAnalyst->name,
                'reassigned_by' => Auth::user()->name,
                'reason' => $validated['reassignment_reason']
            ]);
        });

        return redirect()->back()
            ->with('success', 'Sampel berhasil ditugaskan ulang');
    }

    /**
     * Preview assignment form (F.2.7.1.0.02 format)
     */
    public function previewForm($id)
    {
        $sample = Sample::with(['sampleType', 'parameters', 'assignment.analyst'])->findOrFail($id);
        
        return view('assignments.preview-form', compact('sample'));
    }

    /**
     * Print assignment form
     */
    public function printForm($id)
    {
        $sample = Sample::with(['sampleType', 'parameters', 'assignment.analyst'])->findOrFail($id);
        
        return view('assignments.print-form', compact('sample'));
    }

    /**
     * Get available analysts based on sample parameters
     */
    private function getAvailableAnalysts($sample)
    {
        $parameterIds = $sample->parameters->pluck('id')->toArray();
        
        return User::whereIn('role', ['ANALYST', 'SUPERVISOR_ANALYST'])
            ->where('is_active', true)
            ->whereHas('specializations', function ($query) use ($parameterIds) {
                $query->whereIn('parameter_id', $parameterIds);
            })
            ->with(['specializations'])
            ->get();
    }

    /**
     * Get best analyst based on specialization and current workload
     */
    private function getBestAnalyst($sample)
    {
        $parameterIds = $sample->parameters->pluck('id')->toArray();
        
        // Get analysts with specialization in required parameters
        $analysts = User::whereIn('role', ['ANALYST', 'SUPERVISOR_ANALYST'])
            ->where('is_active', true)
            ->whereHas('specializations', function ($query) use ($parameterIds) {
                $query->whereIn('parameter_id', $parameterIds);
            })
            ->withCount(['assignments' => function ($query) {
                $query->where('status', 'assigned');
            }])
            ->orderBy('assignments_count', 'asc')
            ->first();

        return $analysts;
    }

    /**
     * Get current workload for all analysts
     */
    private function getAnalystWorkload()
    {
        return User::whereIn('role', ['ANALYST', 'SUPERVISOR_ANALYST'])
            ->where('is_active', true)
            ->withCount(['assignments' => function ($query) {
                $query->where('status', 'assigned');
            }])
            ->get()
            ->keyBy('id');
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
