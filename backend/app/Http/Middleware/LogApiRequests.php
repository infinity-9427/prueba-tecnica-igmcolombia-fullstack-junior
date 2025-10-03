<?php

namespace App\Http\Middleware;

use App\Services\LogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    public function __construct(
        private LogService $logService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log request start
        $this->logService->log('debug', 'API Request Started', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'input' => $this->sanitizeInput($request->all()),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
        ]);

        $response = $next($request);

        $duration = microtime(true) - $startTime;

        // Log API request completion
        $this->logService->logApiRequest(
            $request->method(),
            $request->getPathInfo(),
            $response->getStatusCode(),
            $duration,
            [
                'request_size' => strlen($request->getContent()),
                'response_size' => strlen($response->getContent()),
            ]
        );

        // Log slow requests
        if ($duration > 2) {
            $this->logService->logPerformance(
                'Slow API Request',
                $duration,
                [
                    'method' => $request->method(),
                    'endpoint' => $request->getPathInfo(),
                    'status_code' => $response->getStatusCode(),
                ]
            );
        }

        // Log errors
        if ($response->getStatusCode() >= 400) {
            $this->logService->log('warning', 'API Request Error', [
                'method' => $request->method(),
                'endpoint' => $request->getPathInfo(),
                'status_code' => $response->getStatusCode(),
                'response' => $this->sanitizeResponse($response),
            ]);
        }

        return $response;
    }

    /**
     * Sanitize request input to remove sensitive data
     */
    private function sanitizeInput(array $input): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($input[$key])) {
                $input[$key] = '[REDACTED]';
            }
        }

        return $input;
    }

    /**
     * Sanitize headers to remove sensitive data
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'cookie', 'x-api-key'];
        
        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = ['[REDACTED]'];
            }
        }

        return $headers;
    }

    /**
     * Sanitize response data
     */
    private function sanitizeResponse(Response $response): array
    {
        $content = $response->getContent();
        
        if (strlen($content) > 1000) {
            $content = substr($content, 0, 1000) . '... [TRUNCATED]';
        }

        return [
            'status' => $response->getStatusCode(),
            'content' => $content,
            'headers' => $response->headers->all(),
        ];
    }
}