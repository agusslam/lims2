<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'description',
        'uploaded_by',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer'
    ];

    /**
     * Get the sample for this file
     */
    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    /**
     * Get the user who uploaded this file
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        return in_array(strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
    }

    /**
     * Check if file is a document
     */
    public function getIsDocumentAttribute()
    {
        return in_array(strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION)), ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
    }
}
