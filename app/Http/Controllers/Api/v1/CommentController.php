<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, string $articleIdOrSlug): JsonResponse
    {
        $article = Article::published()
            ->where(function ($q) use ($articleIdOrSlug) {
                $q->where('id', $articleIdOrSlug)->orWhere('slug', $articleIdOrSlug);
            })
            ->firstOrFail();

        $comment = $article->comments()->create([
            'parent_id' => $request->parent_id,
            'author_name' => $request->author_name,
            'author_email' => $request->author_email,
            'content' => $request->content,
            'rating' => $request->rating,
            'status' => 'approved',
        ]);

        if ($request->rating && $request->rating >= 1 && $request->rating <= 5) {
            $avg = $article->comments()->whereNotNull('rating')->avg('rating') ?: 5.0;
            $count = $article->comments()->whereNotNull('rating')->count();
            $article->update([
                'rating_average' => round($avg, 1),
                'ratings_count' => $count,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Comment posted successfully',
            'data' => $comment
        ], 201);
    }

    /**
     * Delete comment after verifying commenter's email address
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $comment = Comment::findOrFail($id);

        if (strtolower(trim($comment->author_email)) !== strtolower(trim($request->email))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email verification failed! The provided email does not match the original commenter.'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully.'
        ]);
    }
}
