<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SampleTest extends Model
{
    use HasFactory;

    protected $table = 'sample_tests';

    protected $fillable = [
        'sample_id',
        'test_parameter_id',
        'result',
        'result_value',
        'notes',
        'method_used',
        'tested_by',
        'tested_at',
        'status'
    ];

    protected $casts = [
        'tested_at' => 'datetime',
        'instrument_files' => 'array',
    ];

    /**
     * Get the sample this test belongs to
     */
    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    /**
     * Get the parameter being tested
     */
    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameters_id'); 
    }

    /**
     * Get the user who performed the test
     */
    public function testedBy()
    {
        return $this->belongsTo(User::class, 'tested_by');
    }
}
