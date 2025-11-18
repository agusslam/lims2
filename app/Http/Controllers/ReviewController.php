<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Sample;
use App\Models\SampleReview;
use App\Helpers\CodeGenerator;

class ReviewController extends Controller
{
    /**
     * Display samples for review
     */
    public function index()
    {
        $query = Sample::with(['sampleType', 'parameters', 'results', 'assignment.analyst']);
        
        // Filter based on user role
        if (Auth::user()->role === 'TECH_AUDITOR') {
            $query->where('status', 'review_tech');
        } elseif (Auth::user()->role === 'QUALITY_AUDITOR') {
            $query->where('status', 'review_quality');
        } else {
            $query->whereIn('status', ['review_tech', 'review_quality']);
        }
        
        $samples = $query->orderBy('testing_completed_at', 'desc')->paginate(20);

        return view('review.index', compact('samples'));
    }

    /**
     * Show sample for review
     */
    public function show($id)
    {
        $sample = Sample::with([
            'sampleType', 
            'parameters', 
            'results.parameter',
            'assignment.analyst',
            'files',
            'reviews.reviewer'
        ])->findOrFail($id);

        // Check if user has access to review this sample
        $allowedStatuses = [];
        if (Auth::user()->role === 'TECH_AUDITOR') {
            $allowedStatuses = ['review_tech'];
        } elseif (Auth::user()->role === 'QUALITY_AUDITOR') {
            $allowedStatuses = ['review_quality'];
        } else {
            $allowedStatuses = ['review_tech', 'review_quality'];
        }

        if (!in_array($sample->status, $allowedStatuses)) {
            abort(403, 'Unauthorized access to review this sample');
        }

        return view('review.show', compact('sample'));
    }

    /**
     * Preview review form
     */
    public function preview($id)
    {
        $sample = Sample::with([
            'sampleType', 
            'parameters', 
            'results.parameter',
            'assignment.analyst',
            'reviews.reviewer'
        ])->findOrFail($id);
        
        return view('review.preview', compact('sample'));
    }

    /**
     * Approve technical review
     */
    public function approveTechnical(Request $request, $id)
    {
        $sample = Sample::findOrFail($id);
        
        if ($sample->status !== 'review_tech') {
            return redirect()->back()->with('error', 'Sampel tidak dalam status review teknis');
        }

        $validated = $request->validate([
            'technical_notes' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($sample, $validated) {
            // Create review record
            SampleReview::create([
                'sample_id' => $sample->id,
                'reviewer_id' => Auth::id(),
                'review_type' => 'technical',
                'status' => 'approved',
                'notes' => $validated['technical_notes'],
                'recommendations' => $validated['recommendations'],
                'reviewed_at' => now()
            ]);

            // Update sample status to quality review
            $sample->update([
                'status' => 'review_quality',
                'tech_reviewed_at' => now(),
                'tech_reviewed_by' => Auth::id()
            ]);
            
            // Log audit trail
            $this->logAudit('technical_review_approved', $sample->id, [
                'reviewed_by' => Auth::user()->name
            ]);
        });

        return redirect()->route('review.index')
            ->with('success', 'Review teknis disetujui, sampel diteruskan ke review mutu');
    }

    /**
     * Reject technical review (back to testing)
     */
    public function rejectTechnical(Request $request, $id)
    {
        $sample = Sample::findOrFail($id);
        
        if ($sample->status !== 'review_tech') {
            return redirect()->back()->with('error', 'Sampel tidak dalam status review teknis');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'correction_required' => 'required|string|max:1000'
        ]);

        DB::transaction(function () use ($sample, $validated) {
            // Create review record
            SampleReview::create([
                'sample_id' => $sample->id,
                'reviewer_id' => Auth::id(),
                'review_type' => 'technical',
                'status' => 'rejected',
                'notes' => $validated['rejection_reason'],
                'correction_required' => $validated['correction_required'],
                'reviewed_at' => now()
            ]);

            // Update sample status back to retesting
            $sample->update([
                'status' => 'retesting',
                'retesting_reason' => $validated['rejection_reason'],
                'retesting_required' => $validated['correction_required'],
                'returned_to_testing_at' => now()
            ]);
            
            // Log audit trail
            $this->logAudit('technical_review_rejected', $sample->id, [
                'reviewed_by' => Auth::user()->name,
                'reason' => $validated['rejection_reason']
            ]);
        });

        return redirect()->route('review.index')
            ->with('success', 'Review teknis ditolak, sampel dikembalikan untuk pengujian ulang');
    }

    /**
     * Approve quality review
     */
    public function approveQuality(Request $request, $id)
    {
        $sample = Sample::findOrFail($id);
        
        if ($sample->status !== 'review_quality') {
            return redirect()->back()->with('error', 'Sampel tidak dalam status review mutu');
        }

        $validated = $request->validate([
            'quality_notes' => 'nullable|string|max:1000',
            'certificate_required' => 'required|boolean',
            'final_recommendations' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($sample, $validated) {
            // Create review record
            SampleReview::create([
                'sample_id' => $sample->id,
                'reviewer_id' => Auth::id(),
                'review_type' => 'quality',
                'status' => 'approved',
                'notes' => $validated['quality_notes'],
                'recommendations' => $validated['final_recommendations'],
                'reviewed_at' => now()
            ]);

            // Determine next status based on certificate requirement
            $nextStatus = $validated['certificate_required'] ? 'validated' : 'completed';
            
            $sample->update([
                'status' => $nextStatus,
                'certificate_required' => $validated['certificate_required'],
                'quality_reviewed_at' => now(),
                'quality_reviewed_by' => Auth::id(),
                'validated_at' => now()
            ]);
            
            // Log audit trail
            $this->logAudit('quality_review_approved', $sample->id, [
                'reviewed_by' => Auth::user()->name,
                'certificate_required' => $validated['certificate_required']
            ]);
        });

        $message = $validated['certificate_required'] 
            ? 'Review mutu disetujui, sampel siap untuk penerbitan sertifikat'
            : 'Review mutu disetujui, sampel selesai tanpa sertifikat';

        return redirect()->route('review.index')
            ->with('success', $message);
    }

    /**
     * Reject quality review (back to testing)
     */
    public function rejectQuality(Request $request, $id)
    {
        $sample = Sample::findOrFail($id);
        
        if ($sample->status !== 'review_quality') {
            return redirect()->back()->with('error', 'Sampel tidak dalam status review mutu');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'correction_required' => 'required|string|max:1000'
        ]);

        DB::transaction(function () use ($sample, $validated) {
            // Create review record
            SampleReview::create([
                'sample_id' => $sample->id,
                'reviewer_id' => Auth::id(),
                'review_type' => 'quality',
                'status' => 'rejected',
                'notes' => $validated['rejection_reason'],
                'correction_required' => $validated['correction_required'],
                'reviewed_at' => now()
            ]);

            // Generate retesting code
            $retestingCode = CodeGenerator::generateRetestingCode($sample->sample_code);
            
            // Archive current sample
            $sample->update([
                'status' => 'archived',
                'archived_reason' => 'quality_review_rejected',
                'archived_at' => now()
            ]);
            
            // Create new sample for retesting
            $newSample = $sample->replicate();
            $newSample->sample_code = $retestingCode;
            $newSample->status = 'retesting';
            $newSample->retesting_reason = $validated['rejection_reason'];
            $newSample->retesting_required = $validated['correction_required'];
            $newSample->parent_sample_id = $sample->id;
            $newSample->save();
            
            // Copy parameters and assignment
            $newSample->parameters()->sync($sample->parameters->pluck('id'));
            
            // Log audit trail
            $this->logAudit('quality_review_rejected_retesting', $sample->id, [
                'reviewed_by' => Auth::user()->name,
                'new_sample_code' => $retestingCode,
                'reason' => $validated['rejection_reason']
            ]);
        });

        return redirect()->route('review.index')
            ->with('success', 'Review mutu ditolak, sampel baru dibuat untuk pengujian ulang');
    }

    /**
     * Print review form
     */
    public function printForm($id)
    {
        $sample = Sample::with([
            'sampleType', 
            'parameters', 
            'results.parameter',
            'assignment.analyst',
            'reviews.reviewer'
        ])->findOrFail($id);
        
        return view('review.print-form', compact('sample'));
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
