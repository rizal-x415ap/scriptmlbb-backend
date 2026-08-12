<?php

use App\Http\Controllers\Api\v1\ArticleController;
use App\Http\Controllers\Api\v1\CommentController;
use App\Http\Controllers\Api\v1\HomeFeedController;
use App\Http\Controllers\Api\v1\PremiumTokenController;
use App\Http\Controllers\Api\v1\SettingController;
use App\Http\Controllers\Api\v1\ShortLinkController;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\VerifyApiKey;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')
    ->middleware([ForceJsonResponse::class, SecurityHeadersMiddleware::class, VerifyApiKey::class])
    ->group(function () {

        // Site Settings (Public for all visitors)
        Route::get('/settings', [SettingController::class, 'publicIndex'])
            ->middleware('throttle:120,1');

        // Admin Authentication & Settings Update (Protected by Sanctum Token)
        Route::post('/admin/login', [SettingController::class, 'adminLogin'])
            ->middleware('throttle:10,1');

        Route::post('/admin/settings', [SettingController::class, 'updateSettings'])
            ->middleware(['throttle:30,1', 'auth:sanctum']);

        // Home Feed
        Route::get('/home-feed', HomeFeedController::class)
            ->middleware('throttle:60,1');

        // Topics List (Public, throttle 60/min)
        Route::get('/topics', [ArticleController::class, 'topics'])
            ->middleware('throttle:60,1');

        // Popular Articles (Public, throttle 60/min)
        Route::get('/articles/popular', [ArticleController::class, 'popular'])
            ->middleware('throttle:60,1');

        // Articles Feed & Search
        Route::get('/articles', [ArticleController::class, 'index'])
            ->middleware('throttle:30,1');

        // Article Details
        Route::get('/articles/{idOrSlug}', [ArticleController::class, 'show'])
            ->middleware('throttle:60,1');

        // Article Like (Throttled 10 req/min per IP)
        Route::post('/articles/{id}/like', [ArticleController::class, 'like'])
            ->middleware('throttle:10,1');

        // Article Comment Submission (Throttled: 30 req / 1 min)
        Route::post('/articles/{id}/comments', [CommentController::class, 'store'])
            ->middleware('throttle:30,1');

        // Comment Deletion with Email Verification
        Route::delete('/comments/{id}', [CommentController::class, 'destroy'])
            ->middleware('throttle:15,1');

        // Static Pages (Tentang Kami, Privacy Policy, Terms, etc.)
        Route::get('/pages', [\App\Http\Controllers\Api\v1\PageController::class, 'index'])
            ->middleware('throttle:60,1');

        Route::get('/pages/{slug}', [\App\Http\Controllers\Api\v1\PageController::class, 'show'])
            ->middleware('throttle:60,1');

        // Short Links (Download Shortener System)
        Route::get('/go/{code}', [ShortLinkController::class, 'resolve'])
            ->middleware('throttle:60,1');

        Route::post('/go/{code}/unlock', [ShortLinkController::class, 'getOriginalUrl'])
            ->middleware('throttle:30,1');

        // Premium Token Subscription Routes
        Route::post('/premium/activate', [PremiumTokenController::class, 'activate'])
            ->middleware('throttle:15,1');

        Route::post('/premium/verify', [PremiumTokenController::class, 'verify'])
            ->middleware('throttle:60,1');
    });
