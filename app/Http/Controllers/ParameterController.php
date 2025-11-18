<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Parameter;
use App\Models\SampleType;

class ParameterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display parameter management index
     */
    public function index()
    {
        $parameters = Parameter::with(['sampleTypes'])
            ->orderBy('category', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(20);

        $sampleTypes = SampleType::withCount('parameters')
            ->orderBy('name', 'asc')
            ->get();

        $parametersByCategory = Parameter::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return view('parameters.index', compact('parameters', 'sampleTypes', 'parametersByCategory'));
    }

    /**
     * Show form to create new parameter
     */
    public function create()
    {
        $sampleTypes = SampleType::orderBy('name', 'asc')->get();
        $categories = Parameter::distinct()->pluck('category')->filter()->sort();
        
        return view('parameters.create', compact('sampleTypes', 'categories'));
    }

    /**
     * Store new parameter
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:parameters,code',
            'category' => 'required|string|max:100',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'data_type' => 'required|in:numeric,text,boolean,date',
            'is_active' => 'boolean',
            'sample_types' => 'array'
        ]);

        DB::transaction(function () use ($validated) {
            $parameter = Parameter::create($validated);
            
            if (!empty($validated['sample_types'])) {
                $parameter->sampleTypes()->sync($validated['sample_types']);
            }
            
            $this->logAudit('parameter_created', $parameter->id, [
                'created_by' => Auth::user()->name,
                'parameter_name' => $parameter->name,
                'category' => $parameter->category
            ]);
        });

        return redirect()->route('parameters.index')
            ->with('success', 'Parameter berhasil ditambahkan');
    }

    /**
     * Show parameter details
     */
    public function show($id)
    {
        $parameter = Parameter::with(['sampleTypes'])->findOrFail($id);
        
        return view('parameters.show', compact('parameter'));
    }

    /**
     * Show form to edit parameter
     */
    public function edit($id)
    {
        $parameter = Parameter::with(['sampleTypes'])->findOrFail($id);
        $sampleTypes = SampleType::orderBy('name', 'asc')->get();
        $categories = Parameter::distinct()->pluck('category')->filter()->sort();
        
        return view('parameters.edit', compact('parameter', 'sampleTypes', 'categories'));
    }

    /**
     * Update parameter
     */
    public function update(Request $request, $id)
    {
        $parameter = Parameter::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:parameters,code,' . $id,
            'category' => 'required|string|max:100',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'data_type' => 'required|in:numeric,text,boolean,date',
            'is_active' => 'boolean',
            'sample_types' => 'array'
        ]);

        DB::transaction(function () use ($parameter, $validated) {
            $parameter->update($validated);
            
            if (isset($validated['sample_types'])) {
                $parameter->sampleTypes()->sync($validated['sample_types']);
            }
            
            $this->logAudit('parameter_updated', $parameter->id, [
                'updated_by' => Auth::user()->name,
                'changes' => $validated
            ]);
        });

        return redirect()->route('parameters.index')
            ->with('success', 'Parameter berhasil diperbarui');
    }

    /**
     * Delete parameter
     */
    public function destroy($id)
    {
        $parameter = Parameter::findOrFail($id);
        
        DB::transaction(function () use ($parameter) {
            $parameter->sampleTypes()->detach();
            $parameter->delete();
            
            $this->logAudit('parameter_deleted', $parameter->id, [
                'deleted_by' => Auth::user()->name,
                'parameter_name' => $parameter->name
            ]);
        });

        return redirect()->route('parameters.index')
            ->with('success', 'Parameter berhasil dihapus');
    }

    /**
     * Sample Types Management
     */
    public function sampleTypes()
    {
        $sampleTypes = SampleType::withCount('parameters')
            ->orderBy('name', 'asc')
            ->paginate(15);
            
        return view('parameters.sample-types', compact('sampleTypes'));
    }

    /**
     * Store new sample type
     */
    public function storeSampleType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sample_types,name',
            'code' => 'required|string|max:50|unique:sample_types,code',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        $sampleType = SampleType::create($validated);
        
        $this->logAudit('sample_type_created', $sampleType->id, [
            'created_by' => Auth::user()->name,
            'sample_type_name' => $sampleType->name
        ]);

        return redirect()->route('parameters.sample-types')
            ->with('success', 'Jenis sampel berhasil ditambahkan');
    }

    /**
     * Get parameters by sample type (AJAX)
     */
    public function getParametersBySampleType($sampleTypeId)
    {
        $parameters = Parameter::whereHas('sampleTypes', function($query) use ($sampleTypeId) {
            $query->where('sample_types.id', $sampleTypeId);
        })->where('is_active', true)
        ->orderBy('category')
        ->orderBy('name')
        ->get(['id', 'name', 'code', 'category', 'unit', 'description']);

        return response()->json($parameters);
    }

    /**
     * Log audit trail
     */
    private function logAudit($action, $recordId, $details = [])
    {
        DB::table('audit_logs')->insert([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => 'parameters',
            'record_id' => $recordId,
            'old_values' => null,
            'new_values' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }
}
