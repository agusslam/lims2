<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SampleType;
use App\Models\Parameter;
use App\Models\SampleRequest;
use App\Helpers\CodeGenerator;

class PublicController extends Controller
{
    /**
     * Show sample request form
     */
    public function sampleRequest()
    {
        try {
            $sampleTypes = SampleType::where("is_active", true)
                ->orderBy("name")
                ->get();

            $parameters = Parameter::where("is_active", true)
                ->orderBy("category")
                ->orderBy("name")
                ->get()
                ->groupBy("category");

            return view(
                "public.sample-request",
                compact("sampleTypes", "parameters"),
            );
        } catch (\Exception $e) {
            // Log error but don't expose to user
            \Log::error(
                "Error loading sample request form: " . $e->getMessage(),
            );

            // Return view with empty data
            return view("public.sample-request", [
                "sampleTypes" => collect(),
                "parameters" => collect(),
            ]);
        }
    }

    /**
     * Submit sample request
     */
    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            "phone" => "required|string|max:20",
            "contact_person" => "required|string|max:255",
            "company_name" => "required|string|max:255",
            "city" => "required|string|max:100",
            "email" => "required|email|max:255",
            "address" => "required|string",
            "sample_type_id" => "required",
            "custom_sample_type" => "nullable|string|max:255",
            "quantity" => "required|integer|min:1|max:50",
            "parameters" => "required|array|min:1",
            "customer_requirements" => "nullable|string",
            "captcha_verified" => "required|accepted",
            "terms_accepted" => "required|accepted",
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Generate tracking code
                $trackingCode = CodeGenerator::generatePublicCode();

                // Create sample request
                $sampleRequest = SampleRequest::create([
                    "tracking_code" => $trackingCode,
                    "contact_person" => $validated["contact_person"],
                    "company_name" => $validated["company_name"],
                    "phone" => $validated["phone"],
                    "email" => $validated["email"],
                    "address" => $validated["address"],
                    "city" => $validated["city"],
                    "sample_type_id" =>
                        $validated["sample_type_id"] === "other"
                            ? null
                            : $validated["sample_type_id"],
                    "custom_sample_type" =>
                        $validated["custom_sample_type"] ?? null,
                    "quantity" => $validated["quantity"],
                    "customer_requirements" =>
                        $validated["customer_requirements"],
                    "status" => "pending",
                    "submitted_at" => now(),
                ]);

                // Attach parameters
                $sampleRequest->parameters()->sync($validated["parameters"]);
            });
            $trackingCode = null; // Initialize with a default value
            return redirect()
                ->route("public.request-success", ["code" => $trackingCode])
                ->with("success", "Permohonan berhasil dikirim");
        } catch (\Exception $e) {
            \Log::error("Error submitting sample request: " . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    "error",
                    "Terjadi kesalahan saat mengirim permohonan. Silahkan coba lagi.",
                );
        }
    }

    /**
     * Show success page with tracking code
     */
    public function requestSuccess($code)
    {
        $request = SampleRequest::where("tracking_code", $code)->firstOrFail();

        return view("public.request-success", compact("request"));
    }

    /**
     * Show tracking form
     */
    public function tracking()
    {
        return view("public.tracking");
    }

    /**
     * Track sample by code
     */
    public function trackSample(Request $request)
    {
        $validated = $request->validate([
            "tracking_code" => "required|string",
        ]);

        $sample = SampleRequest::where(
            "tracking_code",
            $validated["tracking_code"],
        )
            ->orWhereHas("sample", function ($query) use ($validated) {
                $query->where("sample_code", $validated["tracking_code"]);
            })
            ->first();

        if (!$sample) {
            return redirect()
                ->back()
                ->with("error", "Kode tracking tidak ditemukan");
        }

        return view("public.track-result", compact("sample"));
    }

    /**
     * Show feedback form
     */
    public function feedback($code)
    {
        // For now, just return a placeholder view.
        // In a real scenario, you'd fetch the sample request
        // and show a feedback form specific to that request.
        return view("public.feedback", compact("code"));
    }

    /**
     * Show landing page
     */
    public function landing()
    {
        $stats = [
            "total_samples" => SampleRequest::count(),
            "completed_samples" => SampleRequest::where(
                "status",
                "completed",
            )->count(),
            "active_parameters" => Parameter::where("is_active", true)->count(),
        ];

        return view("public.welcome", compact("stats"));
    }
}
