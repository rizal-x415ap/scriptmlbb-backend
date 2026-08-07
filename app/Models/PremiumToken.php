<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PremiumToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'device_fingerprint',
        'device_name',
        'activated_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public static function generateToken(int $length = 5): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $token .= $chars[rand(0, strlen($chars) - 1)];
            }
        } while (static::where('token', $token)->exists());

        return $token;
    }

    public function isValid(?string $fingerprint = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) {
            return false;
        }

        if ($fingerprint !== null && $this->device_fingerprint !== null) {
            return $this->device_fingerprint === $fingerprint;
        }

        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('expires_at', '>', Carbon::now());
    }
}
