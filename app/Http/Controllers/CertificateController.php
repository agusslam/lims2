<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Sample;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;
use PDF;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with(['sample.sampleRequest.customer', 'issuedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending_certificates' => Sample::where('status', 'validated')->count(),
            'issued_today' => Certificate::where('status', 'issued')
                ->whereDate('issued_at', today())->count(),
            'total_certificates' => Certificate::where('status', 'issued')->count()
        ];

        return view('certificates.index', compact('certificates', 'stats'));
    }

    public function show($id)
    {
        $certificate = Certificate::with([
            'sample.sampleRequest.customer',
            'sample.sampleType',
            'sample.tests.testParameter',
            'issuedBy'
        ])->findOrFail($id);

        return view('certificates.show', compact('certificate'));
    }

    public function create()
    {
        $samples = Sample::with(['sampleRequest.customer', 'sampleType'])
            ->where('status', 'validated')
            ->orderBy('updated_at', 'asc')
            ->get();

        $templates = [
            'standard' => 'Sertifikat Standar',
            'water_quality' => 'Sertifikat Kualitas Air',
            'soil_analysis' => 'Sertifikat Analisis Tanah',
            'food_safety' => 'Sertifikat Keamanan Pangan'
        ];

        return view('certificates.create', compact('samples', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sample_id' => 'required|exists:samples,id',
            'template_type' => 'required|string',
            'validity_period' => 'required|integer|min:1|max:365',
            'additional_notes' => 'nullable|string|max:1000'
        ]);

        $sample = Sample::with([
            'sampleRequest.customer',
            'sampleType',
            'tests.testParameter'
        ])->findOrFail($request->sample_id);

        if ($sample->status !== 'validated') {
            return back()->with('error', 'Sampel belum tervalidasi untuk penerbitan sertifikat');
        }

        // Prepare certificate data
        $certificateData = [
            'sample_info' => [
                'sample_code' => $sample->sample_code,
                'sample_type' => $sample->sampleType->name,
                'quantity' => $sample->quantity,
                'description' => $sample->description
            ],
            'customer_info' => [
                'contact_person' => $sample->sampleRequest->customer->contact_person,
                'company_name' => $sample->sampleRequest->customer->company_name,
                'address' => $sample->sampleRequest->customer->address,
                'city' => $sample->sampleRequest->customer->city
            ],
            'test_results' => $sample->tests->map(function($test) {
                return [
                    'parameter' => $test->testParameter->name,
                    'result' => $test->result_value,
                    'unit' => $test->unit,
                    'method' => $test->method
                ];
            })->toArray(),
            'metadata' => [
                'issued_date' => now()->format('Y-m-d'),
                'validity_period' => $request->validity_period,
                'expires_at' => now()->addDays($request->validity_period)->format('Y-m-d'),
                'additional_notes' => $request->additional_notes
            ]
        ];

        $certificate = Certificate::create([
            'sample_id' => $sample->id,
            'template_type' => $request->template_type,
            'certificate_data' => $certificateData,
            'status' => 'draft',
            'expires_at' => now()->addDays($request->validity_period)
        ]);

        $certificate->certificate_number = $certificate->generateCertificateNumber();
        $certificate->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'certificate_created',
            'model_type' => Certificate::class,
            'model_id' => $certificate->id,
            'description' => "Certificate {$certificate->certificate_number} created for sample {$sample->sample_code}"
        ]);

        return redirect()->route('certificates.show', $certificate->id)
            ->with('success', 'Draft sertifikat berhasil dibuat');
    }

    public function issue(Request $request, $id)
    {
        $certificate = Certificate::with('sample')->findOrFail($id);

        if ($certificate->status !== 'draft') {
            return back()->with('error', 'Sertifikat sudah diterbitkan atau dibatalkan');
        }

        // Generate PDF certificate
        $pdfPath = $this->generateCertificatePDF($certificate);

        $certificate->update([
            'status' => 'issued',
            'issued_at' => now(),
            'issued_by' => auth()->id(),
            'file_path' => $pdfPath,
            'digital_signature' => $this->generateDigitalSignature($certificate)
        ]);

        // Update sample status
        $certificate->sample->update(['status' => 'certificated']);

        // Check if all samples in request are completed
        $this->checkAndUpdateSampleRequestStatus($certificate->sample->sampleRequest);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'certificate_issued',
            'model_type' => Certificate::class,
            'model_id' => $certificate->id,
            'description' => "Certificate {$certificate->certificate_number} issued"
        ]);

        return back()->with('success', 'Sertifikat berhasil diterbitkan');
    }

    public function preview($id)
    {
        $certificate = Certificate::with([
            'sample.sampleRequest.customer',
            'sample.sampleType'
        ])->findOrFail($id);

        return view('certificates.preview', compact('certificate'));
    }

    public function download($id)
    {
        $certificate = Certificate::findOrFail($id);

        if ($certificate->status !== 'issued' || !$certificate->file_path) {
            abort(404, 'Sertifikat belum tersedia untuk diunduh');
        }

        if (!Storage::disk('public')->exists($certificate->file_path)) {
            // Regenerate PDF if not found
            $certificate->file_path = $this->generateCertificatePDF($certificate);
            $certificate->save();
        }

        return Storage::disk('public')->download(
            $certificate->file_path,
            "Certificate_{$certificate->certificate_number}.pdf"
        );
    }

    private function generateCertificatePDF($certificate)
    {
        $pdf = PDF::loadView('certificates.templates.' . $certificate->template_type, [
            'certificate' => $certificate,
            'data' => $certificate->certificate_data
        ]);

        $filename = "certificates/{$certificate->certificate_number}.pdf";
        $pdfPath = "certificates/" . date('Y/m') . "/{$certificate->certificate_number}.pdf";
        
        Storage::disk('public')->put($pdfPath, $pdf->output());

        return $pdfPath;
    }

    private function generateDigitalSignature($certificate)
    {
        // Simple digital signature using hash
        $data = $certificate->certificate_number . 
                $certificate->sample_id . 
                $certificate->issued_at->toDateTimeString() .
                config('app.key');
        
        return hash('sha256', $data);
    }

    private function checkAndUpdateSampleRequestStatus($sampleRequest)
    {
        $totalSamples = $sampleRequest->samples()->count();
        $completedSamples = $sampleRequest->samples()
            ->whereIn('status', ['completed', 'certificated'])
            ->count();

        if ($totalSamples === $completedSamples) {
            $sampleRequest->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }
    }
}
