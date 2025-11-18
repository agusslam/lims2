<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Sample;
use App\Http\Controllers\Api\SampleTrackingController as ApiSampleTrackingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware("api")->group(function () {
    // Health check endpoint
    Route::get("/health", function () {
        try {
            // Check database connection
            DB::connection()->getPdo();

            // Get system stats
            $stats = [
                "database" => "connected",
                "samples_count" => Sample::count(),
                "pending_samples" => Sample::where(
                    "status",
                    "pending",
                )->count(),
                "testing_samples" => Sample::where(
                    "status",
                    "testing",
                )->count(),
                "completed_samples" => Sample::where(
                    "status",
                    "completed",
                )->count(),
                "php_version" => PHP_VERSION,
                "laravel_version" => app()->version(),
                "lims_version" => config("lims.version"),
                "timestamp" => now()->toISOString(),
            ];

            return response()->json(
                [
                    "status" => "healthy",
                    "message" => "LIMS system is operational",
                    "data" => $stats,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "unhealthy",
                    "message" => "LIMS system has issues",
                    "error" => $e->getMessage(),
                    "timestamp" => now()->toISOString(),
                ],
                503,
            );
        }
    });

    // Sample tracking API (public)
    Route::get("/track/{code}", function ($code) {
        try {
            $sample = Sample::where("tracking_code", $code)
                ->orWhere("sample_code", $code)
                ->first();

            if (!$sample) {
                return response()->json(
                    [
                        "status" => "not_found",
                        "message" => "Sample not found",
                    ],
                    404,
                );
            }

            return response()->json([
                "status" => "found",
                "data" => [
                    "tracking_code" => $sample->tracking_code,
                    "status" => $sample->status,
                    "status_name" => config(
                        "lims.sample_status.{$sample->status}.name",
                    ),
                    "created_at" => $sample->created_at,
                    "updated_at" => $sample->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Unable to track sample",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    });

    // System statistics (internal use)
    Route::middleware("auth:sanctum")->get("/stats", function () {
        return response()->json([
            "samples_by_status" => Sample::select(
                "status",
                DB::raw("count(*) as count"),
            )
                ->groupBy("status")
                ->get(),
            "samples_by_month" => Sample::select(
                DB::raw("YEAR(created_at) as year"),
                DB::raw("MONTH(created_at) as month"),
                DB::raw("count(*) as count"),
            )
                ->groupBy("year", "month")
                ->orderBy("year", "desc")
                ->orderBy("month", "desc")
                ->limit(12)
                ->get(),
            "recent_activity" => Sample::latest()
                ->limit(10)
                ->get(["id", "tracking_code", "status", "created_at"]),
        ]);
    });
});

// Public API routes (no authentication required)
Route::prefix("v1")->group(function () {
    Route::get("track/{tracking_code}", [
        ApiSampleTrackingController::class,
        "track",
    ]);
    Route::get("sample-types", [
        ApiSampleTrackingController::class,
        "sampleTypes",
    ]);
    Route::get("parameters", [ApiSampleTrackingController::class, "parameters"]);
});

// Protected API routes - temporarily disabled until Sanctum is properly installed
// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('samples', SampleController::class);
//     Route::post('samples/{sample}/files', [SampleController::class, 'uploadFiles']);
//     Route::get('notifications', [NotificationController::class, 'index']);
//     Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
// });
Route::get("track/{tracking_code}", [ApiSampleTrackingController::class, "track"]);
Route::get("sample-types", [ApiSampleTrackingController::class, "sampleTypes"]);
Route::get("parameters", [ApiSampleTrackingController::class, "parameters"]);
