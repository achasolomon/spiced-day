<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectUserTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // If user doesn't have a timezone set, try to detect it from browser
            if (!$user->timezone || $user->timezone === 'UTC') {
                $browserTimezone = $request->input('timezone') ?? $request->header('X-User-Timezone');
                
                if ($browserTimezone && in_array($browserTimezone, timezone_identifiers_list())) {
                    $user->update(['timezone' => $browserTimezone]);
                }
            }
        }
        
        return $next($request);
    }
}
