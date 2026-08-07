<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ShortLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShortLinkController extends Controller
{
    /**
     * Resolve short link code and return details + random article
     */
    public function resolve(string $code): JsonResponse
    {
        $shortLink = ShortLink::where('code', $code)->firstOrFail();
        $shortLink->increment('clicks_count');

        // Fetch 1 random published standard editorial article (excluding playstore/app/script articles)
        $randomArticle = Article::with(['category', 'author', 'tags'])
            ->published()
            ->where('template', '!=', 'playstore')
            ->whereDoesntHave('category', function ($q) {
                $q->where('name', 'like', '%app%')
                  ->orWhere('name', 'like', '%script%')
                  ->orWhere('name', 'like', '%aplikasi%');
            })
            ->where('id', '!=', $shortLink->article_id)
            ->inRandomOrder()
            ->first();

        if (!$randomArticle) {
            $randomArticle = Article::with(['category', 'author', 'tags'])
                ->published()
                ->where('template', '!=', 'playstore')
                ->inRandomOrder()
                ->first();
        }

        if (!$randomArticle) {
            $randomArticle = Article::with(['category', 'author', 'tags'])
                ->published()
                ->first();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'code' => $shortLink->code,
                'link_name' => $shortLink->link_name,
                'article_id' => $shortLink->article_id,
                'random_article' => $randomArticle,
            ]
        ]);
    }

    /**
     * Get the original URL after countdown completes
     */
    public function getOriginalUrl(string $code): JsonResponse
    {
        $shortLink = ShortLink::where('code', $code)->firstOrFail();

        return response()->json([
            'status' => 'success',
            'original_url' => $shortLink->original_url,
        ]);
    }
}
