<?php

namespace App\Services;

use App\Models\Sample;
use Illuminate\Support\Facades\DB;

class SampleCodeService
{
    public static function generateTrackingCode(): string
    {
        $prefix = config('lims.codes.tracking_prefix', 'UNEJ');
        $year = date('Y');
        $month = date('m');
        
        return DB::transaction(function () use ($prefix, $year, $month) {
            // Lock table to prevent duplicate codes
            $lastCode = Sample::where('tracking_code', 'like', $prefix . $year . $month . '%')
                             ->lockForUpdate()
                             ->orderByDesc('tracking_code')
                             ->value('tracking_code');
            
            $sequence = 1;
            if ($lastCode) {
                $sequencePart = substr($lastCode, -6);
                $sequence = intval($sequencePart) + 1;
            }
            
            return $prefix . $year . $month . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        });
    }

    public static function generateSampleCode(): string
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        return DB::transaction(function () use ($year, $month, $day) {
            $datePrefix = $year . $month . $day;
            
            $lastCode = Sample::whereNotNull('sample_code')
                             ->where('sample_code', 'like', $datePrefix . '%')
                             ->lockForUpdate()
                             ->orderByDesc('sample_code')
                             ->value('sample_code');
            
            $sequence = 1;
            if ($lastCode) {
                $sequencePart = substr($lastCode, -6);
                $sequence = intval($sequencePart) + 1;
            }
            
            return $datePrefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        });
    }

    public static function generateRevisionCode(Sample $sample, int $revision = 1): string
    {
        $baseCode = $sample->sample_code ?: self::generateSampleCode();
        return $baseCode . '-REV' . $revision;
    }
}
