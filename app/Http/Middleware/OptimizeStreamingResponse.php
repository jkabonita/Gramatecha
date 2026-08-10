<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeStreamingResponse
{
    /**
     * Handle an incoming request.
     * Optimizes streaming responses by disabling unnecessary features.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Disable session for streaming requests (no session writes)
        config(['session.driver' => 'array']);
        
        return $next($request);
    }
}
