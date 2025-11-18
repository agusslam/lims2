<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'test_parameter_id',
        'result',
        'notes',
        'method_used',
        'tested_by',
        'tested_at'
    ];

    protected $casts = [
        'tested_at' => 'datetime'
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
        return $this->belongsTo(TestParameter::class, 'test_parameter_id');
    }

    /**
     * Get the user who performed the test
     */
    public function testedBy()
    {
        return $this->belongsTo(User::class, 'tested_by');
    }
}
