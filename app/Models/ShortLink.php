<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'article_id',
        'original_url',
        'link_name',
        'clicks_count',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public static function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = Str::random($length);
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
