<?php

namespace App\Http\Middleware;

use App\Models\Api\ApiClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiClientIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->user();

        if (! $client instanceof ApiClient) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! $client->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'API client is disabled.',
            ], 403);
        }

        $client->forceFill([
            'last_used_at' => now(),
        ])->saveQuietly();

        return $next($request);
    }
}
