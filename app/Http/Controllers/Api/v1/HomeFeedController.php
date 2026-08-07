<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class HomeFeedController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $data = \Illuminate\Support\Facades\Cache::remember('api_home_feed_data', 300, function () {
            $featured = Article::with(['category', 'author'])
                ->published()
                ->featured()
                ->latest('published_at')
                ->first();

            $feed = Article::with(['category', 'author'])
                ->published()
                ->orderByDesc('is_featured')
                ->latest('published_at')
                ->paginate(15);

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

            return [
                'featured' => $featured,
                'feed' => $feed->items(),
                'topics' => $topics,
                'pagination' => [
                    'current_page' => $feed->currentPage(),
                    'last_page' => $feed->lastPage(),
                    'total' => $feed->total(),
                ]
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ])->header('Cache-Control', 'public, max-age=300, s-maxage=300');
    }
}
