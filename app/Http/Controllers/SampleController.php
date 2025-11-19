<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Sample;
use App\Models\SampleRequest;
use App\Models\Parameter;
use App\Models\User;
use App\Helpers\CodeGenerator;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class SampleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display pending sample requests (Daftar Sampel Baru)
     */
    public function index()
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $samples = SampleRequest::with(['sampleType', 'parameters'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalPending = SampleRequest::where('status', 'pending')->count();
        $totalRegistered = Sample::where('status', 'registered')->count();

        return view('samples.index', compact('samples', 'totalPending', 'totalRegistered'));
    }

    /**
     * Show sample request for validation
     */
    public function show($id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $request = SampleRequest::with(['sampleType', 'parameters'])->findOrFail($id);
        
        return view('samples.show', compact('request'));
    }

    /**
     * Preview sample request with F.2.7.1.0.01 format
     */
    public function preview($id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $request = SampleRequest::with(['sampleType', 'parameters'])->findOrFail($id);
        
        return view('samples.preview', compact('request'));
    }

    /**
     * Edit sample request
     */
    public function edit($id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $request = SampleRequest::with(['sampleType', 'parameters'])->findOrFail($id);
        $sampleTypes = \App\Models\SampleType::all();
        $parameters = Parameter::all()->groupBy('category');
        
        return view('samples.edit', compact('request', 'sampleTypes', 'parameters'));
    }

    /**
     * Update sample request
     */
    public function update(Request $request, $id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $sampleRequest = SampleRequest::findOrFail($id);
        
        $validated = $request->validate([
            'contact_person' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'sample_type_id' => 'required|exists:sample_types,id',
            'quantity' => 'required|integer|min:1',
            'parameters' => 'required|array|min:1',
            'customer_requirements' => 'nullable|string'
        ]);

        DB::transaction(function () use ($sampleRequest, $validated) {
            $sampleRequest->update($validated);
            $sampleRequest->parameters()->sync($validated['parameters']);
            
            // Log audit trail
            $this->logAudit('sample_request_updated', $sampleRequest->id, [
                'updated_by' => Auth::user()->name,
                'changes' => $validated
            ]);
        });

        return redirect()->route('samples.show', $id)
            ->with('success', 'Data permohonan berhasil diperbarui');
    }

    /**
     * Approve and register sample (Setujui)
     */
    public function approve($id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $request = SampleRequest::with(['parameters'])->findOrFail($id);
        
        DB::transaction(function () use ($request) {
            // Generate codes
            $trackingCode = $request->tracking_code;
            $sampleCode = CodeGenerator::generateInternalCode();
            
            // Create sample record
            $sample = Sample::create([
                'tracking_code' => $trackingCode,
                'sample_code' => $sampleCode,
                'customer_name' => $request->contact_person,
                'company_name' => $request->company_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'sample_type_id' => $request->sample_type_id,
                'quantity' => $request->quantity,
                'customer_requirements' => $request->customer_requirements,
                'status' => 'registered',
                'registered_by' => Auth::id(),
                'registered_at' => now()
            ]);

            // Sync parameters
            $sample->parameters()->sync($request->parameters->pluck('id')->toArray());
            
            // Update request status
            $request->update(['status' => 'approved']);
            
            // Log audit trail
            $this->logAudit('sample_approved', $sample->id, [
                'approved_by' => Auth::user()->name,
                'sample_code' => $sampleCode,
                'tracking_code' => $trackingCode
            ]);
        });

        return redirect()->route('samples.index')
            ->with('success', 'Sampel berhasil disetujui dan terdaftar dengan kode: ' . $request->sample_code);
    }

    /**
     * Reject sample request (Tolak)
     */
    public function reject(Request $request, $id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $sampleRequest = SampleRequest::findOrFail($id);
        
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        DB::transaction(function () use ($sampleRequest, $validated) {
            $sampleRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'rejected_by' => Auth::id(),
                'rejected_at' => now()
            ]);
            
            // Log audit trail
            $this->logAudit('sample_rejected', $sampleRequest->id, [
                'rejected_by' => Auth::user()->name,
                'reason' => $validated['rejection_reason']
            ]);
        });

        return redirect()->route('samples.index')
            ->with('success', 'Permohonan sampel berhasil ditolak');
    }

    /**
     * Archive sample request (Arsip)
     */
    public function archive($id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $request = SampleRequest::findOrFail($id);
        
        DB::transaction(function () use ($request) {
            $request->update([
                'status' => 'archived',
                'archived_by' => Auth::id(),
                'archived_at' => now()
            ]);
            
            // Log audit trail
            $this->logAudit('sample_archived', $request->id, [
                'archived_by' => Auth::user()->name
            ]);
        });

        return redirect()->route('samples.index')
            ->with('success', 'Permohonan sampel berhasil diarsipkan');
    }


    public function codification()
{
    // Check permission for Module 2
    if (!Auth::user()->hasPermission(2)) {
        abort(403, 'Unauthorized access to Codification');
    }

    // 1) Prefer samples table (registered)
    $registeredCount = Sample::where('status', 'registered')->count();
    if ($registeredCount > 0) {
        $samples = Sample::with(['sampleType', 'parameters', 'registeredBy'])
            ->where('status', 'registered')
            ->orderBy('registered_at', 'desc')
            ->paginate(20);

        return view('codifications.index', compact('samples'));
    }

    // 2) FALLBACK: jika tidak ada Sample, ambil SampleRequest yang sudah di-approve
    $qr = SampleRequest::with(['sampleType', 'parameters', 'customer'])
        ->where('status', 'approved')
        ->orderBy('submitted_at', 'desc')
        ->paginate(20);

    // Map each SampleRequest into a pseudo-Sample object compatible with view
    $mapped = $qr->getCollection()->map(function ($req) {
        $tests = $req->parameters->map(function ($param) {
            return (object)[ 'testParameter' => $param ];
        });

        return (object)[
            'id' => $req->id,
            'sample_code' => null,
            'status' => 'approved',
            'sampleRequest' => $req,
            'sampleType' => $req->sampleType,
            'custom_sample_type' => null,
            'quantity' => $req->quantity,
            'description' => $req->customer_requirements ?? null,
            'tests' => $tests,
        ];
    });

    // Build paginator from mapped collection
    $samples = new LengthAwarePaginator(
        $mapped,
        $qr->total(),
        $qr->perPage(),
        $qr->currentPage(),
        ['path' => LengthAwarePaginator::resolveCurrentPath()]
    );

    return view('codifications.index', compact('samples'));
}

/** alias */
public function codificationIndex()
{
    return $this->codification();
}

    /**
     * Show sample for codification
     */
    public function showCodification($id)
    {
        // Check permission for Module 2
        if (!Auth::user()->hasPermission(2)) {
            abort(403, 'Unauthorized access to Codification');
        }

        $sample = Sample::with(['sampleType', 'parameters', 'registeredBy'])->findOrFail($id);
        
        return view('samples.codification-show', compact('sample'));
    }

 /**
 * Process codification (Kodifikasi Barang Uji)
 */
public function processCodification(Request $request, $id)
{
    // Check permission for Module 2
    if (!Auth::user()->hasPermission(2)) {
        abort(403, 'Unauthorized access to Codification');
    }

    $sample = Sample::findOrFail($id);

    // Validate only the fields we actually send from the form
    $validated = $request->validate([
        'codification_notes' => 'nullable|string|max:1000',
        'action' => 'required|in:approve,reject',
        // add other fields here if your form sends them
    ]);

    // prepare variables so they are available after transaction
    $message = null;
    $logAction = null;

    DB::transaction(function () use ($sample, $validated, &$message, &$logAction) {
        // Determine action
        $action = $validated['action'];

        if ($action === 'approve') {
            $sample->update([
                'status' => 'codified',
                'codification_notes' => $validated['codification_notes'] ?? null,
                // only include this if your samples table has this column
                'meets_requirements' => 1,
                'codified_by' => Auth::id(),
                'codified_at' => now()
            ]);

            $message = 'Kodifikasi barang uji berhasil disetujui';
            $logAction = 'codification_approved';
        } else {
            // action === 'reject'
            $sample->update([
                'status' => 'registered', // or 'rejected_codification' if you prefer
                'codification_notes' => $validated['codification_notes'] ?? null,
                // only include this if your samples table has this column
                'meets_requirements' => 0,
                'rejected_codification_at' => now()
            ]);

            $message = 'Kodifikasi barang uji ditolak, sampel dikembalikan';
            $logAction = 'codification_rejected';
        }

        // Log audit trail
        $this->logAudit($logAction, $sample->id, [
            'processed_by' => Auth::user()->name,
            'action' => $action,
            'notes' => $validated['codification_notes'] ?? null
        ]);
    });

    // Redirect to codification index (pastikan route name benar)
    return redirect()->route('samples.codification.index')
        ->with('success', $message ?? 'Operasi selesai');
}



    /**
     * Print sample form (F.2.7.1.0.01 format)
     */
    public function printForm($id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample List');
        }

        $sample = Sample::with(['sampleType', 'parameters', 'registeredBy'])->findOrFail($id);
        
        return view('samples.print-form', compact('sample'));
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