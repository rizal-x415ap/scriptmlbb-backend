<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request and verify the frontend API key.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $validKey = config('app.api_key');

        // If an API key is configured on server, strictly enforce it
        if ($validKey && (empty($apiKey) || !hash_equals($validKey, $apiKey))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid or missing API key header.'
            ], 401);
        }

        return $next($request);
    }
}
