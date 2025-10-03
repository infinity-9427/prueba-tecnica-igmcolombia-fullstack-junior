<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceRepository extends BaseRepository
{
    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    /**
     * Get invoices with relationships
     */
    public function findWithRelations(int $id): ?Invoice
    {
        return $this->model->with(['client', 'user', 'items'])->find($id);
    }

    /**
     * Get paginated invoices with filters
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['client', 'user', 'items']);

        $this->applyFilters($query, $filters);

        $sort = [
            'by' => $filters['sort_by'] ?? 'created_at',
            'direction' => $filters['sort_direction'] ?? 'desc'
        ];
        $this->applySorting($query, $sort);

        return $query->paginate($perPage);
    }

    /**
     * Get invoices by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get invoices by client
     */
    public function getByClient(int $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)
            ->with(['items'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get invoices by user
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->with(['client', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent invoices
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->with(['client', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get overdue invoices
     */
    public function getOverdue(): Collection
    {
        return $this->model->where('status', 'pending')
            ->where('due_date', '<', now())
            ->with(['client', 'user'])
            ->get();
    }

    /**
     * Get invoices by date range
     */
    public function getByDateRange(string $dateFrom, string $dateTo): Collection
    {
        return $this->model->whereDate('issue_date', '>=', $dateFrom)
            ->whereDate('issue_date', '<=', $dateTo)
            ->with(['client', 'user', 'items'])
            ->orderBy('issue_date', 'desc')
            ->get();
    }

    /**
     * Get total amount for period
     */
    public function getTotalAmountForPeriod(string $dateFrom, string $dateTo): float
    {
        return $this->model->whereDate('issue_date', '>=', $dateFrom)
            ->whereDate('issue_date', '<=', $dateTo)
            ->sum('total_amount');
    }

    /**
     * Get invoice statistics
     */
    public function getStatistics(): array
    {
        $total = $this->model->count();
        $pending = $this->model->where('status', 'pending')->count();
        $paid = $this->model->where('status', 'paid')->count();
        $overdue = $this->model->where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();

        $totalAmount = $this->model->sum('total_amount');
        $paidAmount = $this->model->where('status', 'paid')->sum('total_amount');
        $pendingAmount = $this->model->where('status', 'pending')->sum('total_amount');

        return [
            'total_invoices' => $total,
            'pending_invoices' => $pending,
            'paid_invoices' => $paid,
            'overdue_invoices' => $overdue,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'pending_amount' => $pendingAmount,
        ];
    }

    /**
     * Search invoices
     */
    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('client', function ($clientQuery) use ($search) {
                  $clientQuery->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Apply invoice-specific filters
     */
    protected function applyFilters($query, array $filters): void
    {
        parent::applyFilters($query, $filters);

        if (isset($filters['invoice_number'])) {
            $query->where('invoice_number', 'like', '%' . $filters['invoice_number'] . '%');
        }

        if (isset($filters['issue_date_from'])) {
            $query->whereDate('issue_date', '>=', $filters['issue_date_from']);
        }

        if (isset($filters['issue_date_to'])) {
            $query->whereDate('issue_date', '<=', $filters['issue_date_to']);
        }

        if (isset($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_from']);
        }

        if (isset($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_to']);
        }

        if (isset($filters['total_amount_min'])) {
            $query->where('total_amount', '>=', $filters['total_amount_min']);
        }

        if (isset($filters['total_amount_max'])) {
            $query->where('total_amount', '<=', $filters['total_amount_max']);
        }
    }
}