<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\SampleParameter;
use Illuminate\Http\Request;

class SampleTrackingController extends Controller
{
    public function track($trackingCode)
    {
        $sample = Sample::with([
            'customer:id,company_name,contact_person',
            'sampleType:id,name',
            'workflowHistory.actionBy:id,name'
        ])->where('tracking_code', $trackingCode)->first();

        if (!$sample) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tracking tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tracking_code' => $sample->tracking_code,
                'sample_code' => $sample->sample_code,
                'status' => $sample->status,
                'status_text' => config('lims.workflow.statuses.' . $sample->status),
                'customer' => $sample->customer,
                'sample_type' => $sample->sampleType,
                'submitted_at' => $sample->submitted_at,
                'workflow_history' => $sample->workflowHistory->map(function($history) {
                    return [
                        'status' => config('lims.workflow.statuses.' . $history->to_status),
                        'action_by' => $history->actionBy->name ?? 'System',
                        'notes' => $history->notes,
                        'action_at' => $history->action_at,
                    ];
                })
            ]
        ]);
    }

    public function sampleTypes()
    {
        $types = SampleType::active()->select('id', 'name', 'description')->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    public function parameters()
    {
        $parameters = SampleParameter::active()
                                   ->select('id', 'category', 'name', 'unit', 'price')
                                   ->get()
                                   ->groupBy('category');

        return response()->json([
            'success' => true,
            'data' => $parameters
        ]);
    }
}
