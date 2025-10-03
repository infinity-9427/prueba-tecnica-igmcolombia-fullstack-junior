<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class LogService
{
    private array $defaultContext = [];

    public function __construct()
    {
        $this->defaultContext = [
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ];
    }

    /**
     * Add user context to logs
     */
    private function addUserContext(array $context = []): array
    {
        $user = Auth::user();
        
        if ($user) {
            $context['user_id'] = $user->id;
            $context['user_email'] = $user->email;
            $context['user_roles'] = $user->getRoleNames()->toArray();
        }

        return array_merge($this->defaultContext, $context);
    }

    /**
     * Log authentication events
     */
    public function logAuth(string $action, string $email, bool $success = true, array $context = []): void
    {
        $level = $success ? 'info' : 'warning';
        
        $logContext = $this->addUserContext([
            'action' => $action,
            'email' => $email,
            'success' => $success,
            'category' => 'authentication',
            ...$context
        ]);

        Log::$level("Authentication: $action", $logContext);
    }

    /**
     * Log invoice operations
     */
    public function logInvoice(string $action, ?int $invoiceId = null, array $context = []): void
    {
        $logContext = $this->addUserContext([
            'action' => $action,
            'invoice_id' => $invoiceId,
            'category' => 'invoice_management',
            ...$context
        ]);

        Log::info("Invoice: $action", $logContext);
    }

    /**
     * Log client operations
     */
    public function logClient(string $action, ?int $clientId = null, array $context = []): void
    {
        $logContext = $this->addUserContext([
            'action' => $action,
            'client_id' => $clientId,
            'category' => 'client_management',
            ...$context
        ]);

        Log::info("Client: $action", $logContext);
    }

    /**
     * Log file operations
     */
    public function logFile(string $action, string $filename, bool $success = true, array $context = []): void
    {
        $level = $success ? 'info' : 'error';
        
        $logContext = $this->addUserContext([
            'action' => $action,
            'filename' => $filename,
            'success' => $success,
            'category' => 'file_management',
            ...$context
        ]);

        Log::$level("File: $action", $logContext);
    }

    /**
     * Log security events
     */
    public function logSecurity(string $event, string $level = 'warning', array $context = []): void
    {
        $logContext = $this->addUserContext([
            'event' => $event,
            'category' => 'security',
            'severity' => $level,
            ...$context
        ]);

        Log::$level("Security: $event", $logContext);
    }

    /**
     * Log performance metrics
     */
    public function logPerformance(string $operation, float $duration, array $context = []): void
    {
        $logContext = $this->addUserContext([
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'category' => 'performance',
            ...$context
        ]);

        $level = $duration > 2 ? 'warning' : 'info';
        Log::$level("Performance: $operation", $logContext);
    }

    /**
     * Log business events
     */
    public function logBusiness(string $event, array $context = []): void
    {
        $logContext = $this->addUserContext([
            'event' => $event,
            'category' => 'business_logic',
            ...$context
        ]);

        Log::info("Business: $event", $logContext);
    }

    /**
     * Log API requests
     */
    public function logApiRequest(string $method, string $endpoint, int $statusCode, float $duration, array $context = []): void
    {
        $level = $statusCode >= 400 ? 'warning' : 'info';
        
        $logContext = $this->addUserContext([
            'method' => $method,
            'endpoint' => $endpoint,
            'status_code' => $statusCode,
            'duration_ms' => round($duration * 1000, 2),
            'category' => 'api_request',
            ...$context
        ]);

        Log::$level("API: $method $endpoint", $logContext);
    }

    /**
     * Log database operations
     */
    public function logDatabase(string $operation, string $table, float $duration, array $context = []): void
    {
        $logContext = $this->addUserContext([
            'operation' => $operation,
            'table' => $table,
            'duration_ms' => round($duration * 1000, 2),
            'category' => 'database',
            ...$context
        ]);

        $level = $duration > 1 ? 'warning' : 'debug';
        Log::$level("Database: $operation on $table", $logContext);
    }

    /**
     * Log errors with full context
     */
    public function logError(\Throwable $exception, array $context = []): void
    {
        $logContext = $this->addUserContext([
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'category' => 'error',
            ...$context
        ]);

        Log::error("Exception: " . get_class($exception), $logContext);
    }

    /**
     * Log cache operations
     */
    public function logCache(string $operation, string $key, bool $hit = true, array $context = []): void
    {
        $logContext = $this->addUserContext([
            'operation' => $operation,
            'cache_key' => $key,
            'cache_hit' => $hit,
            'category' => 'cache',
            ...$context
        ]);

        Log::debug("Cache: $operation", $logContext);
    }

    /**
     * Log user activity for audit trail
     */
    public function logActivity(string $activity, string $resource, ?int $resourceId = null, array $context = []): void
    {
        $logContext = $this->addUserContext([
            'activity' => $activity,
            'resource' => $resource,
            'resource_id' => $resourceId,
            'category' => 'user_activity',
            ...$context
        ]);

        Log::info("Activity: $activity on $resource", $logContext);
    }

    /**
     * Log system events
     */
    public function logSystem(string $event, string $level = 'info', array $context = []): void
    {
        $logContext = array_merge($this->defaultContext, [
            'event' => $event,
            'category' => 'system',
            ...$context
        ]);

        Log::$level("System: $event", $logContext);
    }

    /**
     * Create a structured log entry
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $logContext = $this->addUserContext($context);
        Log::$level($message, $logContext);
    }

    /**
     * Get log context for external use
     */
    public function getContext(array $additionalContext = []): array
    {
        return $this->addUserContext($additionalContext);
    }
}