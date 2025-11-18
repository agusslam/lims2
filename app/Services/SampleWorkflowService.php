<?php

namespace App\Services;

use App\Models\Sample;
use App\Models\SampleWorkflowHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SampleWorkflowService
{
    public static function transitionStatus(Sample $sample, string $newStatus, string $notes = null): bool
    {
        if (!$sample->canTransitionTo($newStatus)) {
            return false;
        }

        return DB::transaction(function () use ($sample, $newStatus, $notes) {
            $oldStatus = $sample->status;
            
            // Update sample status
            $sample->update(['status' => $newStatus]);
            
            // Record workflow history
            SampleWorkflowHistory::create([
                'sample_id' => $sample->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'action_by' => Auth::id(),
                'notes' => $notes,
            ]);
            
            // Execute status-specific actions
            self::executeStatusActions($sample, $newStatus);
            
            return true;
        });
    }

    private static function executeStatusActions(Sample $sample, string $status): void
    {
        switch ($status) {
            case Sample::STATUS_REGISTERED:
                if (!$sample->sample_code) {
                    $sample->update([
                        'sample_code' => SampleCodeService::generateSampleCode(),
                        'verified_by' => Auth::id(),
                        'verified_at' => now()
                    ]);
                }
                break;
                
            case Sample::STATUS_ASSIGNED:
                // Auto-assign to analyst if configured
                if (!$sample->assigned_analyst_id && self::isAutoAssignEnabled()) {
                    $analyst = self::findAvailableAnalyst($sample);
                    if ($analyst) {
                        $sample->update(['assigned_analyst_id' => $analyst->id]);
                    }
                }
                break;
                
            case Sample::STATUS_REJECTED:
                // Create revision if needed
                if ($sample->sample_code && !str_contains($sample->sample_code, '-REV')) {
                    $revisionCode = SampleCodeService::generateRevisionCode($sample);
                    
                    // Archive current sample
                    $sample->update(['status' => Sample::STATUS_ARCHIVED]);
                    
                    // Create new revision sample
                    self::createRevisionSample($sample, $revisionCode);
                }
                break;
        }
    }

    private static function isAutoAssignEnabled(): bool
    {
        return config('lims.workflow.auto_assign', false);
    }

    private static function findAvailableAnalyst(Sample $sample): ?User
    {
        // Find analyst with least workload
        return User::where('role', 'ANALYST')
                  ->where('is_active', true)
                  ->withCount(['assignedSamples' => function ($query) {
                      $query->whereIn('status', ['assigned', 'testing']);
                  }])
                  ->orderBy('assigned_samples_count')
                  ->first();
    }

    private static function createRevisionSample(Sample $originalSample, string $revisionCode): Sample
    {
        $newSample = $originalSample->replicate();
        $newSample->sample_code = $revisionCode;
        $newSample->status = Sample::STATUS_ASSIGNED;
        $newSample->save();

        // Copy test parameters
        foreach ($originalSample->testParameters as $param) {
            $newSample->testParameters()->create([
                'parameter_id' => $param->parameter_id,
            ]);
        }

        return $newSample;
    }
}
