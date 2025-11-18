<?php

namespace App\Services;

use App\Models\Sample;
use App\Models\CustomerFeedback;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    public static function getDashboardStats(string $period = '30_days'): array
    {
        $startDate = self::getPeriodStartDate($period);
        
        return [
            'samples_by_status' => self::getSamplesByStatus($startDate),
            'samples_by_type' => self::getSamplesByType($startDate),
            'analyst_performance' => self::getAnalystPerformance($startDate),
            'customer_satisfaction' => self::getCustomerSatisfaction($startDate),
            'testing_turnaround' => self::getTestingTurnaround($startDate),
            'revenue_analysis' => self::getRevenueAnalysis($startDate),
        ];
    }

    private static function getPeriodStartDate(string $period): Carbon
    {
        return match($period) {
            '7_days' => now()->subDays(7),
            '30_days' => now()->subDays(30),
            '90_days' => now()->subDays(90),
            '1_year' => now()->subYear(),
            default => now()->subDays(30)
        };
    }

    private static function getSamplesByStatus(Carbon $startDate): array
    {
        return Sample::select('status', DB::raw('COUNT(*) as count'))
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();
    }

    private static function getSamplesByType(Carbon $startDate): array
    {
        return Sample::with('sampleType')
                    ->where('created_at', '>=', $startDate)
                    ->get()
                    ->groupBy('sampleType.name')
                    ->map->count()
                    ->toArray();
    }

    private static function getAnalystPerformance(Carbon $startDate): array
    {
        return Sample::select('assigned_analyst_id', 
                            DB::raw('COUNT(*) as total_samples'),
                            DB::raw('AVG(DATEDIFF(updated_at, created_at)) as avg_completion_days'))
                    ->with('assignedAnalyst:id,name')
                    ->whereNotNull('assigned_analyst_id')
                    ->where('status', Sample::STATUS_COMPLETED)
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('assigned_analyst_id')
                    ->get()
                    ->keyBy('assignedAnalyst.name')
                    ->toArray();
    }

    private static function getCustomerSatisfaction(Carbon $startDate): array
    {
        $feedback = CustomerFeedback::where('submitted_at', '>=', $startDate)
                                  ->selectRaw('rating, COUNT(*) as count')
                                  ->groupBy('rating')
                                  ->pluck('count', 'rating')
                                  ->toArray();
        
        $avgRating = CustomerFeedback::where('submitted_at', '>=', $startDate)
                                   ->avg('rating');
        
        return [
            'ratings_distribution' => $feedback,
            'average_rating' => round($avgRating, 2),
            'total_responses' => array_sum($feedback)
        ];
    }

    private static function getTestingTurnaround(Carbon $startDate): array
    {
        return Sample::selectRaw('
                        AVG(DATEDIFF(
                            CASE WHEN status = "completed" THEN updated_at ELSE NOW() END,
                            created_at
                        )) as avg_turnaround_days,
                        MIN(DATEDIFF(updated_at, created_at)) as min_turnaround,
                        MAX(DATEDIFF(updated_at, created_at)) as max_turnaround
                    ')
                    ->where('created_at', '>=', $startDate)
                    ->whereIn('status', [Sample::STATUS_COMPLETED, Sample::STATUS_ARCHIVED])
                    ->first()
                    ->toArray();
    }

    private static function getRevenueAnalysis(Carbon $startDate): array
    {
        $result = DB::table('invoices')
                ->join('samples', 'invoices.sample_id', '=', 'samples.id')
                ->where('samples.created_at', '>=', $startDate)
                ->selectRaw('
                    SUM(invoices.total_amount) as total_revenue,
                    AVG(invoices.total_amount) as avg_invoice_amount,
                    COUNT(*) as total_invoices,
                    SUM(CASE WHEN invoices.status = "paid" THEN invoices.total_amount ELSE 0 END) as paid_revenue
                ')
                ->first();

        return (array) $result;
    }
}
