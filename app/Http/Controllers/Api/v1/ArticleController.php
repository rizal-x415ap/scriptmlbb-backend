<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['category', 'author', 'tags'])
            ->published();

        // Search query filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            if ($category !== 'All') {
                $query->whereHas('category', fn($q) => $q->where('name', $category));
            }
        }

        // Year filter
        if ($year = $request->input('year')) {
            if ($year !== 'All') {
                $query->whereYear('published_at', $year);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        if ($sort === 'popular') {
            $query->orderByDesc('likes_count');
        } elseif ($sort === 'readingTime') {
            $query->orderBy('read_time');
        } else {
            $query->latest('published_at');
        }

        $articles = $query->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $articles->items(),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'total' => $articles->total(),
            ]
        ]);
    }

    public function show(string $idOrSlug): JsonResponse
    {
        $articleData = \Illuminate\Support\Facades\Cache::remember("api_article_detail_{$idOrSlug}", 120, function () use ($idOrSlug) {
            $article = Article::with(['category', 'author', 'tags', 'shortLinks', 'comments' => function ($q) {
                $q->whereNull('parent_id')->approved()->with('replies');
            }])
            ->published()
            ->where(function ($q) use ($idOrSlug) {
                if (is_numeric($idOrSlug)) {
                    $q->where('id', (int)$idOrSlug)->orWhere('slug', $idOrSlug);
                } else {
                    $q->where('slug', $idOrSlug);
                }
            })
            ->first();

            return $article ? $article->toArray() : null;
        });

        if (!$articleData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan.'
            ], 404);
        }

        // Asynchronous non-blocking views count increment
        Article::where('id', $articleData['id'])->increment('views_count');

        return response()->json([
            'status' => 'success',
            'data' => $articleData
        ])->header('Cache-Control', 'public, max-age=60, s-maxage=60');
    }

    public function like(string $idOrSlug): JsonResponse
    {
        $article = Article::published()
            ->where(function ($q) use ($idOrSlug) {
                $q->where('id', $idOrSlug)->orWhere('slug', $idOrSlug);
            })
            ->firstOrFail();
        $article->increment('likes_count');

        return response()->json([
            'status' => 'success',
            'likes_count' => $article->likes_count
        ]);
    }
}
