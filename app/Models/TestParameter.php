<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'unit', 'category', 'price', 'description', 'method', 'specialist_roles', 'is_required', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'specialist_roles' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get sample requests that use this parameter
     */
    public function sampleRequests()
    {
        return $this->belongsToMany(
            SampleRequest::class, 
            'sample_request_parameters', 
            'test_parameter_id', 
            'sample_request_id'
        );
    }

    /**
     * Scope for active parameters
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
