<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'parent_id',
        'author_name',
        'author_email',
        'author_avatar',
        'content',
        'rating',
        'status',
        'is_author_reply',
    ];

    protected $hidden = [
        'author_email',
    ];

    protected $casts = [
        'is_author_reply' => 'boolean',
        'rating' => 'integer',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', 'approved');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
