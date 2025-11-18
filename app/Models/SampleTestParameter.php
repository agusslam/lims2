<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleTestParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'parameter_id',
        'result_value',
        'result_unit',
        'notes',
        'tested_at',
        'tested_by',
    ];

    protected $casts = [
        'tested_at' => 'datetime',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function parameter()
    {
        return $this->belongsTo(SampleParameter::class);
    }

    public function testedBy()
    {
        return $this->belongsTo(User::class, 'tested_by');
    }
}
