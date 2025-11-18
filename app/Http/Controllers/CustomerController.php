<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\SampleRequest;
use App\Models\AuditLog;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount('samples')
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        $stats = [
            'total_customers' => Customer::count(),
            'verified_customers' => Customer::where('is_verified', true)->count(),
            'pending_verification' => Customer::where('is_verified', false)->count(),
            'active_this_month' => Customer::whereHas('sampleRequests', function($query) {
                $query->whereMonth('created_at', now()->month);
            })->count()
        ];

        return view('customers.index', compact('customers', 'stats'));
    }

    public function show($id)
    {
        $customer = Customer::with([
            'sampleRequests' => function($query) {
                $query->with(['samples'])->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        return view('customers.show', compact('customer'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers',
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $customer = Customer::create($request->all());

        return redirect()->route('customers.show', $customer)
                        ->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $request->validate([
            'contact_person' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'whatsapp_number' => 'required|string|regex:/^[0-9]{10,15}$/',
            'email' => 'required|email',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        $oldValues = $customer->toArray();
        
        $customer->update($request->all());

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'customer_updated',
            'model_type' => Customer::class,
            'model_id' => $customer->id,
            'old_values' => $oldValues,
            'new_values' => $customer->fresh()->toArray(),
            'description' => "Customer {$customer->contact_person} updated"
        ]);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Data pelanggan berhasil diperbarui');
    }

    public function verify(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        if ($customer->is_verified) {
            return back()->with('error', 'Pelanggan sudah terverifikasi');
        }

        $customer->update(['is_verified' => true]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'customer_verified',
            'model_type' => Customer::class,
            'model_id' => $customer->id,
            'description' => "Customer {$customer->contact_person} verified"
        ]);

        return back()->with('success', 'Pelanggan berhasil diverifikasi');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        
        if ($customer->samples()->count() > 0) {
            return back()->withErrors(['delete' => 'Customer tidak dapat dihapus karena masih memiliki sampel.']);
        }

        $customer->delete();

        return redirect()->route('customers.index')
                        ->with('success', 'Customer berhasil dihapus.');
    }
}
