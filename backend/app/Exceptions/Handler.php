<?php

namespace App\Exceptions;

use App\Helpers\ApiResponseHelper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Custom reporting logic can be added here
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response|JsonResponse|SymfonyResponse
    {
        // Always return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions with proper error responses
     */
    private function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        $context = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'request_id' => $request->header('X-Request-ID', uniqid('req_')),
        ];

        // Handle specific exception types
        return match (true) {
            $e instanceof ValidationException => $this->handleValidationException($e, $context),
            $e instanceof AuthenticationException => $this->handleAuthenticationException($e, $context),
            $e instanceof AuthorizationException => $this->handleAuthorizationException($e, $context),
            $e instanceof ModelNotFoundException => $this->handleModelNotFoundException($e, $context),
            $e instanceof QueryException => $this->handleQueryException($e, $context),
            $e instanceof NotFoundHttpException => $this->handleNotFoundHttpException($e, $context),
            $e instanceof MethodNotAllowedHttpException => $this->handleMethodNotAllowedException($e, $context),
            $e instanceof TooManyRequestsHttpException => $this->handleTooManyRequestsException($e, $context),
            $e instanceof HttpException => $this->handleHttpException($e, $context),
            default => $this->handleGenericException($e, $context),
        };
    }

    /**
     * Handle validation exceptions
     */
    private function handleValidationException(ValidationException $e, array $context): JsonResponse
    {
        $errors = $e->errors();
        $isDuplicateEmail = isset($errors['email']) && str_contains(implode(' ', $errors['email']), 'already');
        
        Log::warning('Validation failed', array_merge($context, [
            'errors' => $errors,
            'failed_rules' => $this->getFailedRules($e),
            'is_duplicate' => $isDuplicateEmail,
        ]));

        // Handle duplicate email as a conflict (409) instead of validation error (422)
        if ($isDuplicateEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed - email already exists',
                'error' => 'The email address is already registered in the system',
                'error_code' => 'EMAIL_DUPLICATE',
                'details' => [
                    'field' => 'email',
                    'value' => request()->input('email'),
                    'timestamp' => now()->toISOString(),
                    'request_id' => $context['request_id'],
                ]
            ], 409);
        }

        return response()->json([
            'success' => false,
            'message' => 'The given data was invalid.',
            'error' => 'Validation failed - please check your input data',
            'error_code' => 'VALIDATION_ERROR',
            'errors' => $errors,
            'details' => [
                'failed_rules' => $this->getFailedRules($e),
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 422);
    }

    /**
     * Handle authentication exceptions
     */
    private function handleAuthenticationException(AuthenticationException $e, array $context): JsonResponse
    {
        Log::warning('Authentication failed', array_merge($context, [
            'error' => $e->getMessage(),
            'guards' => $e->guards(),
        ]));

        return response()->json([
            'success' => false,
            'message' => 'Authentication required.',
            'error' => 'You must be authenticated to access this resource.',
            'error_code' => 'AUTHENTICATION_REQUIRED',
            'details' => [
                'login_url' => route('login', [], false),
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 401);
    }

    /**
     * Handle authorization exceptions
     */
    private function handleAuthorizationException(AuthorizationException $e, array $context): JsonResponse
    {
        Log::warning('Authorization failed', array_merge($context, [
            'error' => $e->getMessage(),
            'user_role' => request()->user()?->role,
        ]));

        return response()->json([
            'success' => false,
            'message' => 'Access denied.',
            'error' => 'You do not have permission to perform this action.',
            'error_code' => 'INSUFFICIENT_PERMISSIONS',
            'details' => [
                'required_permission' => $this->extractRequiredPermission($e),
                'user_role' => request()->user()?->role,
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 403);
    }

    /**
     * Handle model not found exceptions
     */
    private function handleModelNotFoundException(ModelNotFoundException $e, array $context): JsonResponse
    {
        $model = class_basename($e->getModel());
        $ids = $e->getIds();

        Log::warning('Model not found', array_merge($context, [
            'model' => $model,
            'ids' => $ids,
        ]));

        return response()->json([
            'success' => false,
            'message' => "{$model} not found.",
            'error' => "No {$model} found with the specified identifier(s).",
            'error_code' => 'RESOURCE_NOT_FOUND',
            'details' => [
                'resource' => strtolower($model),
                'ids' => $ids,
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 404);
    }

    /**
     * Handle database query exceptions
     */
    private function handleQueryException(QueryException $e, array $context): JsonResponse
    {
        $errorInfo = $e->errorInfo ?? [];
        $sqlState = $errorInfo[0] ?? 'Unknown';
        $errorCode = $errorInfo[1] ?? 0;
        $errorMessage = $errorInfo[2] ?? $e->getMessage();

        Log::error('Database query failed', array_merge($context, [
            'sql_state' => $sqlState,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings(),
        ]));

        // Handle specific database errors
        return match ($sqlState) {
            '23000' => $this->handleIntegrityConstraintViolation($e, $context),
            '23502' => $this->handleNotNullViolation($e, $context),
            '23503' => $this->handleForeignKeyViolation($e, $context),
            '23505' => $this->handleUniqueViolation($e, $context),
            '42000' => $this->handleSyntaxError($e, $context),
            '42S02' => $this->handleTableNotFound($e, $context),
            '42S22' => $this->handleColumnNotFound($e, $context),
            '08006', '08001', '08004' => $this->handleConnectionError($e, $context),
            default => $this->handleGenericDatabaseError($e, $context),
        };
    }

    /**
     * Handle integrity constraint violations (duplicates)
     */
    private function handleIntegrityConstraintViolation(QueryException $e, array $context): JsonResponse
    {
        $field = $this->extractConstraintField($e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Data already exists.',
            'error' => "A record with this {$field} already exists in the system.",
            'error_code' => 'DUPLICATE_ENTRY',
            'details' => [
                'field' => $field,
                'constraint_type' => 'unique',
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 409);
    }

    /**
     * Handle foreign key violations
     */
    private function handleForeignKeyViolation(QueryException $e, array $context): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Referenced data does not exist.',
            'error' => 'The referenced resource does not exist or has been deleted.',
            'error_code' => 'FOREIGN_KEY_VIOLATION',
            'details' => [
                'constraint_type' => 'foreign_key',
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 422);
    }

    /**
     * Handle database connection errors
     */
    private function handleConnectionError(QueryException $e, array $context): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Service temporarily unavailable.',
            'error' => 'Unable to connect to the database. Please try again later.',
            'error_code' => 'DATABASE_CONNECTION_ERROR',
            'details' => [
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
                'contact_support' => true,
            ]
        ], 503);
    }

    /**
     * Handle HTTP not found exceptions
     */
    private function handleNotFoundHttpException(NotFoundHttpException $e, array $context): JsonResponse
    {
        Log::warning('Route not found', $context);

        return response()->json([
            'success' => false,
            'message' => 'Endpoint not found.',
            'error' => 'The requested endpoint does not exist.',
            'error_code' => 'ENDPOINT_NOT_FOUND',
            'details' => [
                'url' => $context['url'],
                'method' => $context['method'],
                'available_methods' => $this->getAvailableMethods($context['url']),
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 404);
    }

    /**
     * Handle method not allowed exceptions
     */
    private function handleMethodNotAllowedException(MethodNotAllowedHttpException $e, array $context): JsonResponse
    {
        Log::warning('Method not allowed', array_merge($context, [
            'allowed_methods' => $e->getAllowedMethods(),
        ]));

        return response()->json([
            'success' => false,
            'message' => 'Method not allowed.',
            'error' => "The {$context['method']} method is not allowed for this endpoint.",
            'error_code' => 'METHOD_NOT_ALLOWED',
            'details' => [
                'method_used' => $context['method'],
                'allowed_methods' => $e->getAllowedMethods(),
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 405);
    }

    /**
     * Handle rate limiting exceptions
     */
    private function handleTooManyRequestsException(TooManyRequestsHttpException $e, array $context): JsonResponse
    {
        $retryAfter = $e->getRetryAfter();

        Log::warning('Rate limit exceeded', array_merge($context, [
            'retry_after' => $retryAfter,
        ]));

        return response()->json([
            'success' => false,
            'message' => 'Too many requests.',
            'error' => 'You have exceeded the rate limit. Please try again later.',
            'error_code' => 'RATE_LIMIT_EXCEEDED',
            'details' => [
                'retry_after' => $retryAfter,
                'limit_reset_at' => now()->addSeconds($retryAfter)->toISOString(),
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 429)->header('Retry-After', $retryAfter);
    }

    /**
     * Handle generic HTTP exceptions
     */
    private function handleHttpException(HttpException $e, array $context): JsonResponse
    {
        $statusCode = $e->getStatusCode();
        
        Log::error('HTTP exception', array_merge($context, [
            'status_code' => $statusCode,
            'error' => $e->getMessage(),
        ]));

        return response()->json([
            'success' => false,
            'message' => 'Request failed.',
            'error' => $e->getMessage() ?: 'An HTTP error occurred.',
            'error_code' => 'HTTP_ERROR',
            'details' => [
                'status_code' => $statusCode,
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], $statusCode);
    }

    /**
     * Handle generic exceptions
     */
    private function handleGenericException(Throwable $e, array $context): JsonResponse
    {
        Log::error('Unexpected error', array_merge($context, [
            'exception' => get_class($e),
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]));

        $isProduction = app()->environment('production');

        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred.',
            'error' => $isProduction ? 'Internal server error.' : $e->getMessage(),
            'error_code' => 'INTERNAL_SERVER_ERROR',
            'details' => array_filter([
                'exception_type' => $isProduction ? null : get_class($e),
                'file' => $isProduction ? null : $e->getFile(),
                'line' => $isProduction ? null : $e->getLine(),
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
                'contact_support' => true,
                'support_email' => config('app.support_email', 'support@example.com'),
            ])
        ], 500);
    }

    /**
     * Helper methods
     */
    private function getFailedRules(ValidationException $e): array
    {
        $failedRules = [];
        foreach ($e->errors() as $field => $messages) {
            $failedRules[$field] = $this->extractRulesFromMessages($messages);
        }
        return $failedRules;
    }

    private function extractRulesFromMessages(array $messages): array
    {
        $rules = [];
        foreach ($messages as $message) {
            if (str_contains($message, 'required')) $rules[] = 'required';
            if (str_contains($message, 'email')) $rules[] = 'email';
            if (str_contains($message, 'unique')) $rules[] = 'unique';
            if (str_contains($message, 'min')) $rules[] = 'min';
            if (str_contains($message, 'max')) $rules[] = 'max';
        }
        return array_unique($rules);
    }

    private function extractRequiredPermission(AuthorizationException $e): ?string
    {
        $message = $e->getMessage();
        if (preg_match('/permission[:\s]+([a-zA-Z\-_\.]+)/', $message, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractConstraintField(string $message): string
    {
        // Extract field name from constraint violation message
        if (preg_match('/key \'([^\']+)\'/', $message, $matches)) {
            return $matches[1];
        }
        if (preg_match('/column \'([^\']+)\'/', $message, $matches)) {
            return $matches[1];
        }
        if (preg_match('/users_([^_]+)_unique/', $message, $matches)) {
            return $matches[1];
        }
        return 'data';
    }

    private function getAvailableMethods(string $url): array
    {
        // This would need router introspection - simplified for now
        return ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
    }

    private function handleNotNullViolation(QueryException $e, array $context): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Required data missing.',
            'error' => 'A required field is missing or null.',
            'error_code' => 'REQUIRED_FIELD_MISSING',
            'details' => [
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 422);
    }

    private function handleUniqueViolation(QueryException $e, array $context): JsonResponse
    {
        $field = $this->extractConstraintField($e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Duplicate data detected.',
            'error' => "A record with this {$field} already exists.",
            'error_code' => 'UNIQUE_CONSTRAINT_VIOLATION',
            'details' => [
                'field' => $field,
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
            ]
        ], 409);
    }

    private function handleSyntaxError(QueryException $e, array $context): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Invalid request.',
            'error' => 'The request contains invalid data or syntax.',
            'error_code' => 'INVALID_REQUEST_SYNTAX',
            'details' => [
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
                'contact_support' => true,
            ]
        ], 400);
    }

    private function handleTableNotFound(QueryException $e, array $context): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Service configuration error.',
            'error' => 'A required database table is missing.',
            'error_code' => 'DATABASE_SCHEMA_ERROR',
            'details' => [
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
                'contact_support' => true,
            ]
        ], 503);
    }

    private function handleColumnNotFound(QueryException $e, array $context): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Service configuration error.',
            'error' => 'A required database column is missing.',
            'error_code' => 'DATABASE_SCHEMA_ERROR',
            'details' => [
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
                'contact_support' => true,
            ]
        ], 503);
    }

    private function handleGenericDatabaseError(QueryException $e, array $context): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Database operation failed.',
            'error' => 'A database error occurred while processing your request.',
            'error_code' => 'DATABASE_ERROR',
            'details' => [
                'timestamp' => now()->toISOString(),
                'request_id' => $context['request_id'],
                'contact_support' => true,
            ]
        ], 500);
    }
}