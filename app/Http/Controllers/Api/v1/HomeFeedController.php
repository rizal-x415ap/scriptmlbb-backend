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
        $category = request()->get('category');
        $cacheKey = "api_home_feed_data_page_{$page}_cat_" . md5($category ?? 'all');

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($page, $category) {
            $featured = null;
            if ($page === 1 && (!$category || $category === 'All')) {
                $featured = Article::with(['category', 'author'])
                    ->published()
                    ->featured()
                    ->latest('published_at')
                    ->first();
            }

            $feedQuery = Article::with(['category', 'author'])
                ->published();

            if ($category && $category !== 'All') {
                $feedQuery->whereHas('category', fn($q) => $q->where('name', $category)->orWhere('slug', $category));
            }

            $feed = $feedQuery->orderByDesc('is_featured')
                ->latest('published_at')
                ->paginate(5, ['*'], 'page', $page);

            return [
                'featured' => $featured,
                'feed' => $feed->items(),
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
