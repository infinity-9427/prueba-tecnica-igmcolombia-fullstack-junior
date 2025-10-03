<?php

namespace App\Services;

use App\Interfaces\InvoiceServiceInterface;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(
        private CacheService $cacheService
    ) {}
    public function getAllInvoices(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Invoice::with(['client', 'user', 'items']);

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['invoice_number'])) {
            $query->where('invoice_number', 'like', '%' . $filters['invoice_number'] . '%');
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('issue_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('issue_date', '<=', $filters['date_to']);
        }

        if (isset($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_from']);
        }

        if (isset($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_to']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    public function getInvoiceById(int $id): ?Invoice
    {
        return $this->cacheService->remember(
            "invoice:$id",
            fn() => Invoice::with(['client', 'user', 'items'])->find($id),
            $this->cacheService->getLongTtl()
        );
    }

    public function createInvoice(array $data): Invoice
    {
        DB::beginTransaction();

        try {
            $invoiceData = [
                'invoice_number' => $data['invoice_number'] ?? $this->generateInvoiceNumber(),
                'client_id' => $data['client_id'],
                'user_id' => $data['user_id'],
                'description' => $data['description'] ?? null,
                'additional_notes' => $data['additional_notes'] ?? null,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'status' => $data['status'] ?? 'pending',
                'attachment_path' => $data['attachment_path'] ?? null,
            ];

            $totalAmount = $this->calculateInvoiceTotal($data['items'] ?? []);
            $invoiceData['total_amount'] = $totalAmount;

            $invoice = Invoice::create($invoiceData);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $taxAmount = ($item['unit_price'] * $item['quantity']) * ($item['tax_rate'] / 100);
                    $totalItemAmount = ($item['unit_price'] * $item['quantity']) + $taxAmount;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_rate' => $item['tax_rate'] ?? 19.00,
                        'tax_amount' => $taxAmount,
                        'total_amount' => $totalItemAmount,
                    ]);
                }
            }

            DB::commit();

            Log::info('Invoice created', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'client_id' => $invoice->client_id,
                'total_amount' => $invoice->total_amount
            ]);

            $this->cacheService->clearInvoiceCache($invoice->id);

            return $invoice->load(['client', 'user', 'items']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create invoice', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateInvoice(int $id, array $data): ?Invoice
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return null;
        }

        DB::beginTransaction();

        try {
            $updateData = array_filter([
                'client_id' => $data['client_id'] ?? $invoice->client_id,
                'description' => $data['description'] ?? $invoice->description,
                'additional_notes' => $data['additional_notes'] ?? $invoice->additional_notes,
                'issue_date' => $data['issue_date'] ?? $invoice->issue_date,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
                'status' => $data['status'] ?? $invoice->status,
                'attachment_path' => $data['attachment_path'] ?? $invoice->attachment_path,
            ]);

            if (isset($data['items']) && is_array($data['items'])) {
                $invoice->items()->delete();

                $totalAmount = $this->calculateInvoiceTotal($data['items']);
                $updateData['total_amount'] = $totalAmount;

                foreach ($data['items'] as $item) {
                    $taxAmount = ($item['unit_price'] * $item['quantity']) * ($item['tax_rate'] / 100);
                    $totalItemAmount = ($item['unit_price'] * $item['quantity']) + $taxAmount;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_rate' => $item['tax_rate'] ?? 19.00,
                        'tax_amount' => $taxAmount,
                        'total_amount' => $totalItemAmount,
                    ]);
                }
            }

            $invoice->update($updateData);

            DB::commit();

            $this->cacheService->clearInvoiceCache($invoice->id);

            return $invoice->load(['client', 'user', 'items']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update invoice', [
                'invoice_id' => $id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function deleteInvoice(int $id): bool
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return false;
        }

        try {
            $invoice->delete();

            Log::info('Invoice deleted', [
                'invoice_id' => $id,
                'invoice_number' => $invoice->invoice_number
            ]);

            $this->cacheService->clearInvoiceCache($id);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete invoice', [
                'invoice_id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getRecentInvoices(int $limit = 10): array
    {
        return $this->cacheService->remember(
            'recent_invoices',
            fn() => Invoice::with(['client', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray(),
            $this->cacheService->getShortTtl()
        );
    }

    public function calculateInvoiceTotal(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $subtotal = $item['unit_price'] * $item['quantity'];
            $taxRate = $item['tax_rate'] ?? 19.00;
            $taxAmount = $subtotal * ($taxRate / 100);
            $total += $subtotal + $taxAmount;
        }

        return $total;
    }

    public function updateInvoiceStatus(int $id, string $status): bool
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return false;
        }

        try {
            $invoice->update(['status' => $status]);

            Log::info('Invoice status updated', [
                'invoice_id' => $id,
                'old_status' => $invoice->getOriginal('status'),
                'new_status' => $status
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update invoice status', [
                'invoice_id' => $id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');
        
        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc')
            ->first();

        $sequence = $lastInvoice ? 
            intval(substr($lastInvoice->invoice_number, -4)) + 1 : 
            1;

        return sprintf('%s-%d%s-%04d', $prefix, $year, $month, $sequence);
    }
}