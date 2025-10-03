<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    private int $defaultTtl = 3600; // 1 hour
    private int $shortTtl = 300;    // 5 minutes
    private int $longTtl = 86400;   // 24 hours

    /**
     * Get cached item or store it if it doesn't exist
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $ttl = $ttl ?? $this->defaultTtl;
        
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::error('Cache remember failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to direct callback execution
            return $callback();
        }
    }

    /**
     * Store item in cache
     */
    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        
        try {
            Cache::put($key, $value, $ttl);
            
            Log::info('Cache stored', [
                'key' => $key,
                'ttl' => $ttl
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Cache put failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Get item from cache
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::error('Cache get failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return $default;
        }
    }

    /**
     * Remove item from cache
     */
    public function forget(string $key): bool
    {
        try {
            $result = Cache::forget($key);
            
            Log::info('Cache key forgotten', ['key' => $key]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Cache forget failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Clear cache by pattern
     */
    public function forgetByPattern(string $pattern): int
    {
        try {
            $keys = Cache::store('redis')->keys($pattern);
            $count = 0;
            
            foreach ($keys as $key) {
                if (Cache::forget($key)) {
                    $count++;
                }
            }
            
            Log::info('Cache pattern cleared', [
                'pattern' => $pattern,
                'count' => $count
            ]);
            
            return $count;
        } catch (\Exception $e) {
            Log::error('Cache pattern clear failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }

    /**
     * Cache invoice data
     */
    public function cacheInvoice(int $invoiceId, array $data): bool
    {
        return $this->put("invoice:$invoiceId", $data, $this->longTtl);
    }

    /**
     * Get cached invoice
     */
    public function getCachedInvoice(int $invoiceId): ?array
    {
        return $this->get("invoice:$invoiceId");
    }

    /**
     * Cache invoice list
     */
    public function cacheInvoiceList(string $userId, array $filters, array $data): bool
    {
        $key = "invoices:user:$userId:" . md5(serialize($filters));
        return $this->put($key, $data, $this->shortTtl);
    }

    /**
     * Cache client data
     */
    public function cacheClient(int $clientId, array $data): bool
    {
        return $this->put("client:$clientId", $data, $this->longTtl);
    }

    /**
     * Cache user permissions
     */
    public function cacheUserPermissions(int $userId, array $permissions): bool
    {
        return $this->put("user:permissions:$userId", $permissions, $this->longTtl);
    }

    /**
     * Clear invoice cache
     */
    public function clearInvoiceCache(int $invoiceId): bool
    {
        $this->forget("invoice:$invoiceId");
        return $this->forgetByPattern("invoices:*") > 0;
    }

    /**
     * Clear client cache
     */
    public function clearClientCache(int $clientId): bool
    {
        $this->forget("client:$clientId");
        return $this->forgetByPattern("clients:*") > 0;
    }

    /**
     * Clear user cache
     */
    public function clearUserCache(int $userId): bool
    {
        $this->forget("user:permissions:$userId");
        return $this->forgetByPattern("user:*:$userId") > 0;
    }

    /**
     * Cache dashboard stats
     */
    public function cacheDashboardStats(string $userId, array $stats): bool
    {
        return $this->put("dashboard:stats:$userId", $stats, $this->shortTtl);
    }

    /**
     * Get TTL values
     */
    public function getDefaultTtl(): int
    {
        return $this->defaultTtl;
    }

    public function getShortTtl(): int
    {
        return $this->shortTtl;
    }

    public function getLongTtl(): int
    {
        return $this->longTtl;
    }
}