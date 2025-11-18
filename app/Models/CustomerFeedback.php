<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'rating',
        'comments',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function getRatingStarsAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getRatingTextAttribute()
    {
        $ratings = [
            1 => 'Sangat Tidak Puas',
            2 => 'Tidak Puas', 
            3 => 'Cukup',
            4 => 'Puas',
            5 => 'Sangat Puas'
        ];
        
        return $ratings[$this->rating] ?? 'Unknown';
    }
}
