<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Get all records
     */
    public function all(): Collection;

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find record by ID
     */
    public function find(int $id): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findOrFail(int $id): Model;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update record
     */
    public function update(int $id, array $data): ?Model;

    /**
     * Delete record
     */
    public function delete(int $id): bool;

    /**
     * Find records by criteria
     */
    public function findBy(array $criteria): Collection;

    /**
     * Find first record by criteria
     */
    public function findOneBy(array $criteria): ?Model;

    /**
     * Count records
     */
    public function count(): int;

    /**
     * Check if record exists
     */
    public function exists(int $id): bool;
}