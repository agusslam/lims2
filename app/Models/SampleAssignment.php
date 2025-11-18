<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'analyst_id',
        'assigned_by',
        'priority',
        'deadline',
        'assignment_notes',
        'reassignment_reason',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
        'reassigned_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'reassigned_at' => 'datetime',
        'deadline' => 'datetime'
    ];

    /**
     * Get the sample for this assignment
     */
    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    /**
     * Get the analyst assigned to this sample
     */
    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    /**
     * Get the user who made the assignment
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope for active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'assigned');
    }

    /**
     * Scope for assignments by analyst
     */
    public function scopeByAnalyst($query, $analystId)
    {
        return $query->where('analyst_id', $analystId);
    }
}
