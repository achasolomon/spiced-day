<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('Signature');
        $timestamp = $request->header('X-Timestamp');

        if (!$signature || !str_starts_with($signature, 'sha256=')) {
            return response()->json([
                'error' => 'INTAKE_SIGNATURE_MISSING',
                'message' => 'Missing or malformed Signature header.',
            ], 401);
        }

        if (!$timestamp) {
            return response()->json([
                'error' => 'INTAKE_TIMESTAMP_MISSING',
                'message' => 'Missing X-Timestamp header.',
            ], 401);
        }

        $requestTime = (int) $timestamp;
        if (abs(now()->timestamp - $requestTime) > 300) {
            return response()->json([
                'error' => 'INTAKE_TIMESTAMP_EXPIRED',
                'message' => 'Request timestamp is too old. Max 5 minute window.',
            ], 401);
        }

        $secret = config('services.intake.secret');
        $body = $request->getContent();
        $expected = 'sha256=' . hash_hmac('sha256', $body, $secret);

        if (!hash_equals($expected, $signature)) {
            return response()->json([
                'error' => 'INTAKE_SIGNATURE_INVALID',
                'message' => 'Signature does not match. Check shared secret.',
            ], 401);
        }

        return $next($request);
    }
}
