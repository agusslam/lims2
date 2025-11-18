<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'updated_by',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function get($key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function() use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set($key, $value, $description = null)
    {
        $setting = static::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'description' => $description,
                'updated_by' => auth()->id(),
            ]
        );

        Cache::forget("setting.{$key}");
        return $setting;
    }

    public static function forget($key)
    {
        Cache::forget("setting.{$key}");
        return static::where('key', $key)->delete();
    }

    // Common settings accessors
    public static function getLabName()
    {
        return static::get('lab_name', 'Laboratorium Analisis Tanah dan Tanaman');
    }

    public static function getLabAddress()
    {
        return static::get('lab_address', 'Universitas Jember, Jl. Kalimantan 37, Jember 68121');
    }

    public static function getLabPhone()
    {
        return static::get('lab_phone', '+62-331-329447');
    }

    public static function getLabEmail()
    {
        return static::get('lab_email', 'lab@unej.ac.id');
    }

    public static function getAccreditationNumber()
    {
        return static::get('accreditation_number', 'LP-643-IDN');
    }

    public static function getCertificateValidityDays()
    {
        return (int) static::get('certificate_validity_days', 365);
    }
}
