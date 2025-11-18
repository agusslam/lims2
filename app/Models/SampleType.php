<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Sample type has many parameters
     */
    public function parameters()
    {
        return $this->belongsToMany(Parameter::class, 'parameter_sample_type');
    }

    /**
     * Sample type has many sample requests
     */
    public function sampleRequests()
    {
        return $this->hasMany(SampleRequest::class);
    }

    /**
     * Sample type has many samples
     */
    public function samples()
    {
        return $this->hasMany(Sample::class);
    }

    /**
     * Scope for active sample types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}