<?php

namespace App\Helpers;

use App\Models\SampleRequest;
use App\Models\Sample;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CodeGenerator
{
    /**
     * Generate public tracking code
     * Format: UNEJ202510XXXXXX (4+4+2+6)
     */
    public static function generatePublicCode(): string
    {
        $prefix = config('lims.codes.public_prefix', 'UNEJ');
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        // Get next sequence number
        $sequence = self::getNextSequence('public', 'monthly');
        $sequenceStr = str_pad($sequence, 6, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $month . $sequenceStr;
    }

    /**
     * Generate internal sample code  
     * Format: 20251010XXXXXX (4+4+2+2+6)
     */
    public static function generateInternalCode(): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $day = Carbon::now()->format('d');
        
        // Get next sequence number
        $sequence = self::getNextSequence('internal', 'daily');
        $sequenceStr = str_pad($sequence, 6, '0', STR_PAD_LEFT);
        
        return $year . $month . $day . $sequenceStr;
    }

    /**
     * Generate retesting code
     * Format: Original code + -REV1, -REV2, etc.
     */
    public static function generateRetestingCode(string $originalCode): string
    {
        $revisionSuffix = config('lims.codes.revision_suffix', 'REV');
        
        // Find existing revision numbers
        $existingRevisions = DB::table('samples')
            ->where('sample_code', 'LIKE', $originalCode . '-' . $revisionSuffix . '%')
            ->pluck('sample_code')
            ->toArray();

        $maxRevision = 0;
        foreach ($existingRevisions as $code) {
            if (preg_match('/-' . $revisionSuffix . '(\d+)$/', $code, $matches)) {
                $maxRevision = max($maxRevision, (int)$matches[1]);
            }
        }

        $nextRevision = $maxRevision + 1;
        return $originalCode . '-' . $revisionSuffix . $nextRevision;
    }

    /**
     * Get next sequence number based on reset period
     */
    private static function getNextSequence(string $type, string $resetPeriod): int
    {
        $tableName = 'code_sequences';
        $now = Carbon::now();
        
        // Determine reset date based on period
        switch ($resetPeriod) {
            case 'yearly':
                $resetDate = $now->startOfYear()->format('Y-m-d');
                break;
            case 'monthly':
                $resetDate = $now->startOfMonth()->format('Y-m-d');
                break;
            case 'daily':
                $resetDate = $now->startOfDay()->format('Y-m-d');
                break;
            default:
                $resetDate = $now->startOfMonth()->format('Y-m-d');
        }

        // Get or create sequence record
        $sequence = DB::table($tableName)
            ->where('type', $type)
            ->where('reset_date', $resetDate)
            ->first();

        if (!$sequence) {
            // Create new sequence
            DB::table($tableName)->insert([
                'type' => $type,
                'reset_date' => $resetDate,
                'current_number' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return 1;
        } else {
            // Increment existing sequence
            $nextNumber = $sequence->current_number + 1;
            DB::table($tableName)
                ->where('id', $sequence->id)
                ->update([
                    'current_number' => $nextNumber,
                    'updated_at' => now()
                ]);
            return $nextNumber;
        }
    }

    /**
     * Validate code format
     */
    public static function validatePublicCode(string $code): bool
    {
        $prefix = config('lims.codes.public_prefix', 'UNEJ');
        $pattern = '/^' . $prefix . '\d{4}\d{2}\d{6}$/';
        return preg_match($pattern, $code) === 1;
    }

    /**
     * Validate internal code format
     */
    public static function validateInternalCode(string $code): bool
    {
        $pattern = '/^\d{4}\d{2}\d{2}\d{6}(-REV\d+)?$/';
        return preg_match($pattern, $code) === 1;
    }

    /**
     * Extract date from code
     */
    public static function extractDateFromCode(string $code, string $type = 'public'): ?Carbon
    {
        if ($type === 'public') {
            $prefix = config('lims.codes.public_prefix', 'UNEJ');
            if (preg_match('/^' . $prefix . '(\d{4})(\d{2})/', $code, $matches)) {
                try {
                    return Carbon::createFromFormat('Y-m-d', $matches[1] . '-' . $matches[2] . '-01');
                } catch (\Exception $e) {
                    return null;
                }
            }
        } else {
            if (preg_match('/^(\d{4})(\d{2})(\d{2})/', $code, $matches)) {
                try {
                    return Carbon::createFromFormat('Y-m-d', $matches[1] . '-' . $matches[2] . '-' . $matches[3]);
                } catch (\Exception $e) {
                    return null;
                }
            }
        }
        
        return null;
    }

    /**
     * Check if code exists
     */
    public static function codeExists(string $code, string $table = 'samples', string $column = 'sample_code'): bool
    {
        return DB::table($table)->where($column, $code)->exists();
    }

    /**
     * Generate tracking code
     * Format: TRKYYYYMMDD### (3+8+3)
     */
    public static function generateTrackingCode()
    {
        $prefix = 'TRK';
        $date = date('Ymd');
        
        // Get latest tracking code for today
        $latest = SampleRequest::where('tracking_code', 'like', $prefix . $date . '%')
            ->orderBy('tracking_code', 'desc')
            ->first();
            
        if ($latest) {
            $lastNumber = (int) substr($latest->tracking_code, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . $date . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
