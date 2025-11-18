<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'sample_request_id', 'subtotal', 'tax_rate',
        'tax_amount', 'total_amount', 'status', 'issued_at', 'due_date',
        'paid_at', 'payment_details', 'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'payment_details' => 'array'
    ];

    public function sampleRequest()
    {
        return $this->belongsTo(SampleRequest::class);
    }

    public function isOverdue()
    {
        return $this->status === 'sent' && $this->due_date < now();
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'sent')->where('due_date', '<', now());
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}