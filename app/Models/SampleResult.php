<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'parameter_id',
        'result_value',
        'unit',
        'method',
        'notes',
        'tested_by',
        'tested_at',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'tested_at' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    /**
     * Get the sample for this result
     */
    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    /**
     * Get the parameter for this result
     */
    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }

    /**
     * Get the user who tested this parameter
     */
    public function testedBy()
    {
        return $this->belongsTo(User::class, 'tested_by');
    }

    /**
     * Get the user who reviewed this result
     */
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
