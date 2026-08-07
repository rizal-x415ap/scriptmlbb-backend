<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group'];

    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('api_site_settings');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('api_site_settings');
        });
    }

    /**
     * Get a setting by key with fallback default
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting || $setting->value === null) {
            return $default;
        }

        // Try decoding JSON values if applicable
        $decoded = json_decode($setting->value, true);
        return (json_last_error() === JSON_ERROR_NONE && !is_numeric($setting->value)) ? $decoded : $setting->value;
    }

    /**
     * Set/Update a setting by key
     */
    public static function set(string $key, mixed $value, string $group = 'general'): static
    {
        $stringValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $stringValue, 'group' => $group]
        );
    }
}
