<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sample extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_code',
        'sample_request_id',
        'sample_code',
        'customer_name',
        'company_name',
        'phone',
        'email',
        'address',
        'city',
        'sample_type_id',
        'quantity',
        'customer_requirements',
        'status',
        'registered_by',
        'registered_at',
        'assigned_to',
        'assigned_at',
        'assigned_by',
        'codified_by',
        'codified_at',
        'codification_notes',
        'special_requirements',
        'rejected_codification_at'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'assigned_at' => 'datetime',
        'codified_at' => 'datetime',
        'rejected_codification_at' => 'datetime',
    ];

    /**
     * Get the sample request that owns this sample
     */
    public function sampleRequest()
    {
        return $this->belongsTo(SampleRequest::class);
    }

    /**
     * Get the sample type for this sample
     */
    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }

    /**
     * Get the assigned analyst
     */
    public function assignedAnalyst()
    {
        return $this->belongsTo(User::class, 'assigned_analyst_id');
    }


    public function getTestsAttribute()
{
    // Jika Sample asli -> gunakan relasi parameters sebagai sumber test
    if ($this->relationLoaded('parameters')) {
        return $this->parameters->map(function ($param) {
            return (object)[
                'testParameter' => $param,
            ];
        });
    }

    // Jika relasi belum diload, load dulu
    if (method_exists($this, 'parameters')) {
        $this->load('parameters');
        return $this->parameters->map(function ($param) {
            return (object)[
                'testParameter' => $param,
            ];
        });
    }

    // Jika ini pseudo-object (fallback) dari SampleRequest
    // dan sudah memiliki properti tests, kembalikan apa adanya
    if (property_exists($this, 'tests') && is_array($this->tests)) {
        return collect($this->tests);
    }

    if (property_exists($this, 'tests') && $this->tests instanceof \Illuminate\Support\Collection) {
        return $this->tests;
    }

    // Default fallback: kembalikan collection kosong
    return collect([]);
}
    /**
     * Get the parameters for this sample (using existing sample_tests relationship)
     */
    public function parameters()
    {
        return $this->belongsToMany(
            Parameter::class, 
            'sample_tests', 
            'sample_id', 
            'parameters_id'
        )->withPivot('result', 'notes', 'method_used', 'tested_by', 'tested_at');
    }

    

    /**
     * Get test results for this sample (using existing sample_tests table)
     */
    public function results()
    {
        return $this->hasMany(SampleTest::class);
    }

    /**
     * Get assignment info (using existing assigned_analyst_id field)
     */
    public function assignment()
    {
        return $this->belongsTo(User::class, 'assigned_analyst_id');
    }

    /**
     * Get files uploaded for this sample
     */
    public function files()
    {
        return $this->hasMany(SampleFile::class);
    }

    /**
     * Get reviews for this sample
     */
    public function reviews()
    {
        return $this->hasMany(SampleReview::class);
    }

    // User relationships for workflow tracking
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function codifiedBy()
    {
        return $this->belongsTo(User::class, 'codified_by');
    }

    public function testingStartedBy()
    {
        return $this->belongsTo(User::class, 'testing_started_by');
    }

    public function testingCompletedBy()
    {
        return $this->belongsTo(User::class, 'testing_completed_by');
    }

    public function techReviewedBy()
    {
        return $this->belongsTo(User::class, 'tech_reviewed_by');
    }

    public function qualityReviewedBy()
    {
        return $this->belongsTo(User::class, 'quality_reviewed_by');
    }

    public function parentSample()
    {
        return $this->belongsTo(Sample::class, 'parent_sample_id');
    }

    public function childSamples()
    {
        return $this->hasMany(Sample::class, 'parent_sample_id');
    }

    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Scopes
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $analystId)
    {
        return $query->where('assigned_analyst_id', $analystId);
    }

    // Helper methods
    public function getStatusBadgeAttribute()
    {
        $statusConfig = config('lims.sample_status');
        return $statusConfig[$this->status] ?? ['name' => ucfirst($this->status), 'color' => 'secondary', 'icon' => 'question'];
    }

    public function isRetesting()
    {
        return $this->status === 'retesting' || str_contains($this->sample_code, '-REV');
    }

    public function getRevisionNumber()
    {
        if (preg_match('/-REV(\d+)$/', $this->sample_code, $matches)) {
            return (int)$matches[1];
        }
        return 0;
    }
}