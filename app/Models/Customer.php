<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_person', 'company_name', 'whatsapp_number', 'email',
        'city', 'address', 'latitude', 'longitude', 'is_verified'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_verified' => 'boolean'
    ];

    // Relationships
    public function sampleRequests()
    {
        return $this->hasMany(SampleRequest::class);
    }

    public function samples()
    {
        return $this->hasManyThrough(Sample::class, SampleRequest::class);
    }

    // Accessors & Mutators
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city;
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_verified', false);
    }

    public function hasCoordinates()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}
