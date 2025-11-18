<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\SampleRequest;
use App\Models\AuditLog;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['sampleRequest.customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_invoices' => Invoice::count(),
            'pending_payment' => Invoice::where('status', 'sent')->count(),
            'paid_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count()
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function show($id)
    {
        $invoice = Invoice::with([
            'sampleRequest' => function($query) {
                $query->with(['customer', 'samples.tests.testParameter']);
            }
        ])->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    public function create()
    {
        $sampleRequests = SampleRequest::with(['customer', 'samples'])
            ->where('status', 'completed')
            ->whereDoesntHave('invoices')
            ->get();

        return view('invoices.create', compact('sampleRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sample_request_id' => 'required|exists:sample_requests,id',
            'tax_rate' => 'nullable|numeric|between:0,100',
            'notes' => 'nullable|string|max:1000',
            'due_days' => 'required|integer|min:1|max:90'
        ]);

        $sampleRequest = SampleRequest::with(['samples.tests.testParameter'])
            ->findOrFail($request->sample_request_id);

        // Calculate invoice totals
        $subtotal = $sampleRequest->total_price;
        $taxRate = $request->tax_rate ?? config('lims.default_tax_rate', 0);
        $taxAmount = ($subtotal * $taxRate) / 100;
        $totalAmount = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'sample_request_id' => $sampleRequest->id,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'due_date' => now()->addDays($request->due_days),
            'notes' => $request->notes
        ]);

        $invoice->invoice_number = $this->generateInvoiceNumber($invoice);
        $invoice->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_created',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'description' => "Invoice {$invoice->invoice_number} created"
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice berhasil dibuat');
    }

    public function send(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Invoice sudah dikirim atau dibayar');
        }

        $invoice->update([
            'status' => 'sent',
            'issued_at' => now()
        ]);

        // TODO: Implement email notification to customer

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_sent',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'description' => "Invoice {$invoice->invoice_number} sent to customer"
        ]);

        return back()->with('success', 'Invoice berhasil dikirim ke pelanggan');
    }

    public function markPaid(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        $request->validate([
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
            'paid_amount' => 'required|numeric|min:0',
            'payment_notes' => 'nullable|string'
        ]);

        if ($request->paid_amount < $invoice->total_amount) {
            return back()->with('error', 'Jumlah pembayaran kurang dari total tagihan');
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_details' => [
                'method' => $request->payment_method,
                'reference' => $request->payment_reference,
                'amount' => $request->paid_amount,
                'notes' => $request->payment_notes,
                'processed_by' => auth()->user()->full_name
            ]
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_paid',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'description' => "Invoice {$invoice->invoice_number} marked as paid"
        ]);

        return back()->with('success', 'Invoice berhasil ditandai sebagai lunas');
    }

    public function print($id)
    {
        $invoice = Invoice::with([
            'sampleRequest' => function($query) {
                $query->with(['customer', 'samples.tests.testParameter']);
            }
        ])->findOrFail($id);

        return view('invoices.print', compact('invoice'));
    }

    private function generateInvoiceNumber($invoice)
    {
        $year = date('Y');
        $month = date('m');
        $sequence = str_pad($invoice->id, 4, '0', STR_PAD_LEFT);
        return "INV/{$year}/{$month}/{$sequence}";
    }
}
