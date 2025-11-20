<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_code',
        'contact_person',
        'company_name', 
        'phone',
        'email',
        'address',
        'city',
        'sample_type_id',
        'quantity',
        'customer_requirements',
        'status',
        'urgent',
        'submitted_at',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'customer_id',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'urgent' => 'boolean',
    ];

    /**
     * Get the sample type for this request
     */
    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }

    /**
     * Get the parameters for this request (using existing test_parameters table)
     */
    public function parameters()
    {
        return $this->belongsToMany(
            Parameter::class, 
            'sample_parameter_requests', 
            'sample_request_id', 
            // 'test_parameter_id'
            'parameter_id'
        );
    }

    /**
     * Get the actual sample if this request was approved
     */
    public function sample()
    {
        return $this->hasOne(Sample::class, 'tracking_code', 'tracking_code');
    }

    /**
     * Get the user who rejected this request
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Get formatted submitted date
     */
    public function getFormattedSubmittedAtAttribute()
    {
        return $this->submitted_at ? $this->submitted_at->format('d/m/Y H:i') : '-';
    }

    /**
     * Get total estimated price
     */
    public function getTotalPriceAttribute()
    {
        return $this->parameters->sum(function ($parameter) {
            return $parameter->price * $this->quantity;
        });
    }

    public function generateTrackingCode()
    {
        $year = date('Y');
        $month = date('m');
        $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);
        return "UNEJ{$year}{$month}{$sequence}";
    }

    public static function getNextTrackingCode()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last sequence number for this month
        $lastRequest = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextSequence = $lastRequest ? ($lastRequest->id + 1) : 1;
        
        // If we don't have a last request this month, count all requests to get next ID
        if (!$lastRequest) {
            $nextSequence = self::count() + 1;
        }
        
        $sequence = str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
        return "UNEJ{$year}{$month}{$sequence}";
    }

    public function customer()
    {
        // asumsi nama kolom FK = customer_id
        return $this->belongsTo(Customer::class, 'id');
    }
}

