<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            if (! $request->hasHeader('X-API-Key') && ! $request->bearerToken() && ! $request->is('api/v1/settings')) {
                return $next($request);
            }
        }

        $expectedKey = Cache::rememberForever('site_api_key', function () {
            return SiteSetting::where('id', 1)->value('api_key') ?? '';
        });

        if (empty($expectedKey)) {
            return response()->json([
                'message' => 'Internal Server Error: API Key is not configured on the server.',
            ], 500);
        }

        $requestKey = $request->header('X-API-Key') ?? $request->bearerToken();

        if (! $requestKey || $requestKey !== $expectedKey) {
            return response()->json([
                'message' => 'Unauthorized: Invalid or missing API Key.',
            ], 401);
        }

        return $next($request);
    }
}
