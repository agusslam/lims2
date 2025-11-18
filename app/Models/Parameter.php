<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category',
        'unit',
        'description',
        'min_value',
        'max_value',
        'data_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_value' => 'decimal:4',
        'max_value' => 'decimal:4'
    ];

    /**
     * Parameter belongs to many sample types
     */
    public function sampleTypes()
    {
        return $this->belongsToMany(SampleType::class, 'parameter_sample_type');
    }

    /**
     * Parameter belongs to many sample requests
     */
    public function sampleRequests()
    {
        return $this->belongsToMany(SampleRequest::class, 'sample_parameter_requests');
    }

    /**
     * Parameter belongs to many samples
     */
    public function samples()
    {
        return $this->belongsToMany(Sample::class, 'parameter_sample');
    }

    /**
     * Scope for active parameters
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for required parameters
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}