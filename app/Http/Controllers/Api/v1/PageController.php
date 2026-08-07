<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function index(): JsonResponse
    {
        $pages = Page::where('status', 'published')
            ->select(['id', 'title', 'slug', 'meta_description', 'updated_at'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pages
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }
}
