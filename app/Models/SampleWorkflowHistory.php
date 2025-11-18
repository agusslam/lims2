<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleWorkflowHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'from_status',
        'to_status',
        'action_by',
        'action_date',
        'notes',
        'is_revision',
    ];

    protected $casts = [
        'action_date' => 'datetime',
        'is_revision' => 'boolean',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
