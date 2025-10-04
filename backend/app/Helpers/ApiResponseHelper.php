<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiResponseHelper
{
    /**
     * Success response
     */
    public static function success(
        string $message,
        array $data = [],
        int $statusCode = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Error response
     */
    public static function error(
        string $message,
        string $error = null,
        int $statusCode = 500,
        string $errorCode = null,
        array $details = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];

        if ($error) {
            $response['error'] = $error;
        }

        if ($errorCode) {
            $response['error_code'] = $errorCode;
        }

        if (!empty($details)) {
            $response['details'] = $details;
        }

        // Add support information for server errors
        if ($statusCode >= 500) {
            $response['details'] = array_merge($response['details'] ?? [], [
                'contact_support' => true,
                'support_email' => config('app.support_email', 'support@example.com')
            ]);
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Database error response
     */
    public static function databaseError(
        Throwable $exception,
        array $context = [],
        string $userMessage = 'A database error occurred'
    ): JsonResponse {
        Log::error('Database error', array_merge([
            'error' => $exception->getMessage(),
            'sql_code' => $exception->getCode(),
            'trace' => $exception->getTraceAsString()
        ], $context));

        // Check for specific database errors
        $errorCode = 'DATABASE_ERROR';
        $statusCode = 500;

        if ($exception->getCode() === '23000') {
            $userMessage = 'Data already exists - duplicate entry detected';
            $errorCode = 'DUPLICATE_ENTRY';
            $statusCode = 409;
        } elseif ($exception->getCode() === '23502') {
            $userMessage = 'Required data is missing';
            $errorCode = 'MISSING_REQUIRED_DATA';
            $statusCode = 422;
        } elseif ($exception->getCode() === '23503') {
            $userMessage = 'Referenced data does not exist';
            $errorCode = 'FOREIGN_KEY_VIOLATION';
            $statusCode = 422;
        }

        return self::error(
            'Operation failed due to database error',
            $userMessage,
            $statusCode,
            $errorCode,
            [
                'timestamp' => now()->toISOString()
            ]
        );
    }

    /**
     * Validation error response
     */
    public static function validationError(
        string $message = 'Validation failed',
        array $errors = [],
        array $context = []
    ): JsonResponse {
        Log::warning('Validation error', array_merge([
            'errors' => $errors,
        ], $context));

        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => 'The provided data is invalid',
            'error_code' => 'VALIDATION_ERROR',
            'errors' => $errors,
            'timestamp' => now()->toISOString()
        ], 422);
    }

    /**
     * Authentication error response
     */
    public static function authenticationError(
        string $message = 'Authentication failed',
        string $error = 'Invalid credentials',
        array $context = []
    ): JsonResponse {
        Log::warning('Authentication error', array_merge([
            'message' => $message,
            'error' => $error
        ], $context));

        return self::error(
            $message,
            $error,
            401,
            'AUTHENTICATION_ERROR',
            [
                'timestamp' => now()->toISOString()
            ]
        );
    }

    /**
     * Authorization error response
     */
    public static function authorizationError(
        string $message = 'Access denied',
        string $error = 'Insufficient permissions',
        array $context = []
    ): JsonResponse {
        Log::warning('Authorization error', array_merge([
            'message' => $message,
            'error' => $error
        ], $context));

        return self::error(
            $message,
            $error,
            403,
            'AUTHORIZATION_ERROR',
            [
                'timestamp' => now()->toISOString()
            ]
        );
    }

    /**
     * Not found error response
     */
    public static function notFound(
        string $resource = 'Resource',
        int $id = null,
        array $context = []
    ): JsonResponse {
        $message = $id ? "{$resource} with ID {$id} not found" : "{$resource} not found";

        Log::warning('Resource not found', array_merge([
            'resource' => $resource,
            'id' => $id
        ], $context));

        return self::error(
            $message,
            "The requested {$resource} does not exist",
            404,
            'RESOURCE_NOT_FOUND',
            [
                'resource' => strtolower($resource),
                'id' => $id,
                'timestamp' => now()->toISOString()
            ]
        );
    }

    /**
     * Unexpected error response
     */
    public static function unexpectedError(
        Throwable $exception,
        array $context = [],
        string $userMessage = 'An unexpected error occurred'
    ): JsonResponse {
        Log::error('Unexpected error', array_merge([
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ], $context));

        return self::error(
            'Operation failed due to an unexpected error',
            $userMessage,
            500,
            'UNEXPECTED_ERROR',
            [
                'timestamp' => now()->toISOString(),
                'error_id' => uniqid('err_'),
                'contact_support' => true
            ]
        );
    }

    /**
     * Too many requests error response
     */
    public static function tooManyRequests(
        string $message = 'Too many requests',
        int $retryAfter = 60
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => 'Rate limit exceeded',
            'error_code' => 'RATE_LIMIT_EXCEEDED',
            'details' => [
                'retry_after' => $retryAfter,
                'timestamp' => now()->toISOString()
            ]
        ], 429)->header('Retry-After', $retryAfter);
    }

    /**
     * Service unavailable error response
     */
    public static function serviceUnavailable(
        string $message = 'Service temporarily unavailable',
        string $reason = 'The service is under maintenance'
    ): JsonResponse {
        return self::error(
            $message,
            $reason,
            503,
            'SERVICE_UNAVAILABLE',
            [
                'timestamp' => now()->toISOString(),
                'contact_support' => true
            ]
        );
    }

    /**
     * Created response for successful resource creation
     */
    public static function created(
        string $message,
        array $data = [],
        array $meta = []
    ): JsonResponse {
        return self::success($message, $data, 201, $meta);
    }

    /**
     * No content response for successful deletion
     */
    public static function deleted(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ], 200);
    }
}