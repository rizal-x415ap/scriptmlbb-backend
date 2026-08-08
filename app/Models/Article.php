<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'author_id',
        'template',
        'app_developer',
        'app_version',
        'app_size',
        'app_download_url',
        'download_links',
        'app_screenshots',
        'app_features',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'content',
        'cover_image',
        'app_poster_35',
        'read_time',
        'status',
        'is_featured',
        'likes_count',
        'views_count',
        'rating_average',
        'ratings_count',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'likes_count' => 'integer',
        'views_count' => 'integer',
        'app_screenshots' => 'array',
        'app_features' => 'array',
        'download_links' => 'array',
        'rating_average' => 'float',
        'ratings_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($article) {
            if (empty($article->likes_count)) {
                $article->likes_count = rand(201, 390);
            }
            if (empty($article->views_count)) {
                $article->views_count = rand(1793, 12876);
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('api_home_feed_data');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('api_home_feed_data');
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function shortLinks(): HasMany
    {
        return $this->hasMany(ShortLink::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
