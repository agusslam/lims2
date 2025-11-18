<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'reviewer_id',
        'review_type',
        'status',
        'notes',
        'recommendations',
        'correction_required',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime'
    ];

    /**
     * Get the sample for this review
     */
    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    /**
     * Get the reviewer
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Scope for technical reviews
     */
    public function scopeTechnical($query)
    {
        return $query->where('review_type', 'technical');
    }

    /**
     * Scope for quality reviews
     */
    public function scopeQuality($query)
    {
        return $query->where('review_type', 'quality');
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected reviews
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
