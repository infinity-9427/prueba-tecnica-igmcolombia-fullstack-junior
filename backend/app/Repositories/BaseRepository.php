<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Model
    {
        $record = $this->find($id);
        
        if (!$record) {
            return null;
        }

        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        
        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    public function findBy(array $criteria): Collection
    {
        $query = $this->model->query();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }

    public function findOneBy(array $criteria): ?Model
    {
        $query = $this->model->query();

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->first();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Set model instance
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if (empty($value)) {
                continue;
            }

            switch ($field) {
                case 'search':
                    $this->applySearch($query, $value);
                    break;
                case 'date_from':
                    $query->whereDate('created_at', '>=', $value);
                    break;
                case 'date_to':
                    $query->whereDate('created_at', '<=', $value);
                    break;
                default:
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    } else {
                        $query->where($field, $value);
                    }
            }
        }
    }

    /**
     * Apply search functionality - to be implemented by child classes
     */
    protected function applySearch($query, string $search): void
    {
        // Override in child classes to implement search logic
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting($query, array $sort): void
    {
        $sortBy = $sort['by'] ?? 'id';
        $sortDirection = $sort['direction'] ?? 'desc';

        $query->orderBy($sortBy, $sortDirection);
    }
}