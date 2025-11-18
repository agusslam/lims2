<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Models\Customer;
use App\Models\SampleRequest;
use App\Models\SampleType;
use App\Models\Parameter;
use App\Helpers\CodeGenerator;

class SampleRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of sample requests
     */
    public function index()
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample Requests');
        }

        $requests = SampleRequest::with(['sampleType', 'parameters'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statusCounts = [
            'pending' => SampleRequest::where('status', 'pending')->count(),
            'registered' => SampleRequest::where('status', 'registered')->count(),
            'testing' => SampleRequest::where('status', 'testing')->count(),
            'completed' => SampleRequest::where('status', 'completed')->count(),
        ];

        return view('sample-requests.index', compact('requests', 'statusCounts'));
    }

    /**
     * Show the form for creating a new sample request
     */
    public function create()
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample Requests');
        }

        $sampleTypes = SampleType::where('is_active', true)->orderBy('name')->get();
        $parameters = Parameter::where('is_active', true)->orderBy('category')->orderBy('name')->get();

        return view('sample-requests.create', compact('sampleTypes', 'parameters'));
    }

    /**
     * Store a newly created sample request
     */
    // public function store(Request $request)
    // {
    //     // Check permission for Module 1
    //     if (!Auth::user()->hasPermission(1)) {
    //         abort(403, 'Unauthorized access to Sample Requests');
    //     }

    //     $validated = $request->validate([
    //         'contact_person' => 'required|string|max:255',
    //         'company_name' => 'required|string|max:255',
    //         'phone' => 'required|string|max:20',
    //         'email' => 'required|email|max:255',
    //         'address' => 'required|string',
    //         'city' => 'required|string|max:100',
    //         'sample_type_id' => 'required|exists:sample_types,id',
    //         'quantity' => 'required|integer|min:1',
    //         'parameters' => 'required|array|min:1',
    //         'customer_requirements' => 'nullable|string',
    //         'urgent' => 'boolean'
    //     ]);

    //     // Set customer_id dari user yang login
        

    //     // Generate tracking code jika perlu (contoh sederhana)
    //     // $validated['tracking_code'] = 'TRK-' . strtoupper(uniqid());

    //     // Set default fields jika perlu
    //     // $validated['status'] = $validated['status'] ?? 'pending';
    //     // $validated['submitted_at'] = now();

    //      try { 

    //         DB::transaction(function () use ($validated) {
    //         $trackingCode = CodeGenerator::generateTrackingCode();
    //         $validated['tracking_code'] = $trackingCode;
    //         $validated['status'] = 'pending';
    //         $validated['submitted_at'] = now();
    //         $validated['customer_id'] = "2";

    //         $sampleRequest = SampleRequest::create($validated);
    //         $sampleRequest->parameters()->sync($validated['parameters']);

    //         $this->logAudit('sample_request_created', $sampleRequest->id, [
    //             'created_by' => Auth::user()->name,
    //             'tracking_code' => $trackingCode
    //         ]);
    //     });
    //      }catch(\Throwable $e){
    //          // log error jika perlu
    //        \Log::error('Gagal menyimpan sample request: ' . $e->getMessage(), [
    //     'exception' => $e,
    //     'request' => $request->all()
    // ]);
    //         return redirect()->back()->withInput()->withErrors('Terjadi kesalahan saat menyimpan. Silakan coba lagi.');
    //      } 
        

    //     return redirect()->route('sample-requests.index')
    //         ->with('success', 'Permohonan sampel berhasil dibuat dengan kode tracking: ' . $trackingCode);
    // }

    public function store(Request $request)
{
    // minimal validasi input (parameters dicek lagi secara eksplisit nanti)
    $data = $request->validate([
        'contact_person' => 'required|string|max:191',
        'company_name' => 'nullable|string|max:191',
        'phone' => 'required|string|max:191',
        'email' => 'nullable|email|max:191',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'sample_type_id' => 'required|integer',
        'quantity' => 'required|integer|min:1',
        'parameters' => 'nullable|array',
        'parameters.*' => 'integer',
        'customer_requirements' => 'nullable|string',
        'urgent' => 'sometimes|boolean',
    ]);

    DB::beginTransaction();
    try {
        // Buat / temukan customer (gunakan email jika ada, else pakai phone)
        $customer = null;
        if (!empty($data['email']) || !empty($data['phone'])) {
            $uniqueKey = !empty($data['email']) ? ['email' => $data['email']] : ['phone' => $data['phone']];

            $customer = Customer::firstOrCreate(
                $uniqueKey,
                [
                    'whatsapp_number' => $data['phone'] ?? null,
                    'contact_person'  => $data['contact_person'] ?? null,
                    'company_name'    => $data['company_name'] ?? null,
                    'phone'           => $data['phone'] ?? null,
                    'city'            => $data['city'] ?? null,
                    'address'         => $data['address'] ?? null,
                ]
            );
        }

        // Siapkan payload sample request
        $payload = [
            'contact_person' => $data['contact_person'],
            'company_name' => $data['company_name'] ?? null,
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'sample_type_id' => $data['sample_type_id'],
            'quantity' => $data['quantity'],
            'customer_requirements' => $data['customer_requirements'] ?? null,
            'customer_id' => $customer ? $customer->id : null,
            'tracking_code' => 'TRK-'.strtoupper(uniqid()),
            'status' => 'pending',
            'submitted_at' => now(),
        ];

        // Create sample request (pastikan $fillable di model mengizinkan kolom ini)
        $sample = SampleRequest::create($payload);

        // HANDLE PARAMETERS: ambil hanya ID yang valid di table parameter
        $paramIds = !empty($data['parameters']) ? array_values($data['parameters']) : [];

        if (!empty($paramIds)) {
            // Gunakan model Parameter untuk memeriksa existensi
            $validParamIds = Parameter::whereIn('id', $paramIds)->pluck('id')->toArray();

            // Log jika ada invalid ids
            $invalid = array_diff($paramIds, $validParamIds);
            if (!empty($invalid)) {
                Log::warning('SampleRequest: skipped invalid parameter IDs', [
                    'invalid_parameter_ids' => array_values($invalid),
                    'payload' => $paramIds,
                    'sample_request_temp_id' => $sample->id,
                ]);
            }

            // Jika tidak ada id valid tersisa -> rollback dan beri pesan
            if (empty($validParamIds)) {
                DB::rollBack();
                return redirect()->back()->withInput()->withErrors([
                    'parameters' => 'Tidak ada parameter uji valid yang dipilih. Silakan pilih parameter yang tersedia.'
                ]);
            }

            // Attach hanya yang valid. syncWithoutDetaching aman dari duplikat
            $sample->parameters()->syncWithoutDetaching($validParamIds);
        } else {
            // Jika parameter memang wajib untuk flowmu, rollback dan beri pesan
            // Kalau tidak wajib, kamu bisa menghapus blok ini
            // DB::rollBack();
            // return redirect()->back()->withInput()->withErrors(['parameters' => 'Mohon pilih minimal 1 parameter uji.']);
        }

        DB::commit();

        // Audit log (jika perlu)
        $this->logAudit('sample_request_created', $sample->id, [
            'created_by' => Auth::user()->name ?? null,
            'tracking_code' => $payload['tracking_code']
        ]);

        return redirect()->route('sample-requests.show', $sample->id)->with('success', 'Permohonan berhasil dibuat.');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error creating sample request: '.$e->getMessage(), ['trace'=>$e->getTraceAsString(), 'request' => $request->all()]);
        return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
    }
}


    /**
     * Display the specified sample request
     */
    public function show($id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample Requests');
        }

        $request = SampleRequest::with(['sampleType', 'parameters'])->findOrFail($id);
        
        return view('sample-requests.show', compact('request'));
    }

    /**
     * Show the form for editing the specified sample request
     */
    public function edit($id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample Requests');
        }

        $request = SampleRequest::with(['sampleType', 'parameters'])->findOrFail($id);
        $sampleTypes = SampleType::where('is_active', true)->orderBy('name')->get();
        $parameters = Parameter::where('is_active', true)->orderBy('category')->orderBy('name')->get();

        return view('sample-requests.edit', compact('request', 'sampleTypes', 'parameters'));
    }

    /**
     * Update the status of the specified sample request
     */
    public function updateStatus(Request $request, $id)
    {
        // Check permission for Module 1
        if (!Auth::user()->hasPermission(1)) {
            abort(403, 'Unauthorized access to Sample Requests');
        }

        $sampleRequest = SampleRequest::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,registered,assigned,testing,review,completed',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($sampleRequest, $validated) {
            $sampleRequest->update(['status' => $validated['status']]);
            
            $this->logAudit('status_updated', $sampleRequest->id, [
                'updated_by' => Auth::user()->name,
                'new_status' => $validated['status'],
                'notes' => $validated['notes'] ?? null
            ]);
        });

        return redirect()->back()->with('success', 'Status berhasil diperbarui');
    }

    public function approve(Request $request, $id)
{
    // permission check (sama seperti lainnya)
    if (!Auth::user()->hasPermission(1)) {
        abort(403, 'Unauthorized access to Sample Requests');
    }

    $sampleRequest = SampleRequest::findOrFail($id);

    DB::transaction(function () use ($sampleRequest) {
        // contoh: ubah status menjadi 'registered' saat approve
        $sampleRequest->update([
            'status' => 'registered',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // audit log
        $this->logAudit('sample_request_approved', $sampleRequest->id, [
            'approved_by' => Auth::user()->name ?? null,
            'approved_at' => now()->toDateTimeString()
        ]);
    });

    return redirect()->back()->with('success', 'Permohonan sampel berhasil disetujui.');
}

public function archive(Request $request, $id)
{
    // batasi hanya role tertentu (sama seperti di blade)
    if (!Auth::user()->hasAnyRole(['SUPERVISOR', 'ADMIN', 'DEVEL'])) {
        abort(403, 'Unauthorized');
    }

    $sample = SampleRequest::findOrFail($id);

    DB::transaction(function () use ($sample) {
        // Pastikan kolom archived_at / archived_by ada di tabelmu
        $sample->update([
            'archived_at' => now(),
            'archived_by' => Auth::id(),
            'status' => 'archived' // opsional: ubah status jika ingin
        ]);

        $this->logAudit('sample_request_archived', $sample->id, [
            'archived_by' => Auth::user()->name ?? null,
            'archived_at' => now()->toDateTimeString()
        ]);
    });

    return redirect()->back()->with('success', 'Permohonan sampel berhasil diarsipkan.');
}

public function update(Request $httpRequest, $id)
{
    if (!Auth::user()->hasPermission(1)) {
        abort(403, 'Unauthorized access to Sample Requests');
    }

    $validated = $httpRequest->validate([
        'contact_person' => 'required|string|max:191',
        'company_name' => 'nullable|string|max:191',
        'phone' => 'required|string|max:191',
        'email' => 'nullable|email|max:191',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'sample_type_id' => 'required|integer',
        'quantity' => 'required|integer|min:1',
        'parameters' => 'nullable|array',
        'parameters.*' => 'integer',
        'customer_requirements' => 'nullable|string',
        'urgent' => 'sometimes|boolean',
    ]);

    DB::beginTransaction();
    try {
        $sample = SampleRequest::findOrFail($id);

        $sample->update([
            'contact_person' => $validated['contact_person'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'sample_type_id' => $validated['sample_type_id'],
            'quantity' => $validated['quantity'],
            'customer_requirements' => $validated['customer_requirements'] ?? null,
            'urgent' => isset($validated['urgent']) ? (bool)$validated['urgent'] : $sample->urgent,
        ]);

        // Sync parameters (hanya yang valid)
        $paramIds = !empty($validated['parameters']) ? array_values($validated['parameters']) : [];
        if (!empty($paramIds)) {
            $validParamIds = Parameter::whereIn('id', $paramIds)->pluck('id')->toArray();
            $sample->parameters()->sync($validParamIds);
        } else {
            // Jika ingin mengosongkan parameter saat tidak dipilih, uncomment:
            // $sample->parameters()->sync([]);
        }

        $this->logAudit('sample_request_updated', $sample->id, [
            'updated_by' => Auth::user()->name ?? null,
            'changes' => $sample->getChanges()
        ]);

        DB::commit();

        return redirect()->route('sample-requests.show', $sample->id)->with('success', 'Permohonan berhasil diperbarui.');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error updating sample request: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
    }
}


    private function logAudit($action, $recordId, $details = [])
    {
        DB::table('audit_logs')->insert([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => 'sample_requests',
            'record_id' => $recordId,
            'old_values' => null,
            'new_values' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }
}