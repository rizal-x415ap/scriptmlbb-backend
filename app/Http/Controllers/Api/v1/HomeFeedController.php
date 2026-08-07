<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class HomeFeedController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $page = (int) request()->get('page', 1);
        $cacheKey = "api_home_feed_data_page_{$page}";

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($page) {
            $featured = null;
            if ($page === 1) {
                $featured = Article::with(['category', 'author'])
                    ->published()
                    ->featured()
                    ->latest('published_at')
                    ->first();
            }

            $feed = Article::with(['category', 'author'])
                ->published()
                ->orderByDesc('is_featured')
                ->latest('published_at')
                ->paginate(5, ['*'], 'page', $page);

            $topics = [];
            if ($page === 1) {
                $topics = \App\Models\Category::withCount(['articles' => function ($q) {
                    $q->published();
                }])
                ->orderByDesc('articles_count')
                ->get()
                ->map(function ($cat) {
                    return [
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                        'count' => $cat->articles_count,
                    ];
                });
            }

            return [
                'featured' => $featured,
                'feed' => $feed->items(),
                'topics' => $topics,
                'pagination' => [
                    'current_page' => $feed->currentPage(),
                    'last_page' => $feed->lastPage(),
                    'total' => $feed->total(),
                    'per_page' => $feed->perPage(),
                ]
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ])->header('Cache-Control', 'public, max-age=300, s-maxage=300');
    }
}
