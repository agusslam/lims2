<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'certificate_number',
        'template_type',
        'issued_date',
        'valid_until',
        'issued_by',
        'status',
        'digital_signature',
        'file_path',
        'notes'
    ];

    protected $casts = [
        'issued_date' => 'datetime',
        'valid_until' => 'datetime'
    ];

    /**
     * Get the sample for this certificate
     */
    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    /**
     * Get the user who issued this certificate
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Check if certificate is valid
     */
    public function getIsValidAttribute()
    {
        return $this->valid_until && $this->valid_until->isFuture();
    }

    /**
     * Get days until expiration
     */
    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->valid_until) return null;
        return now()->diffInDays($this->valid_until, false);
    }
}