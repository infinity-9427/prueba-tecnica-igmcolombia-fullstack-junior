<?php

namespace App\Interfaces;

use App\Models\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;

interface InvoiceServiceInterface
{
    public function getAllInvoices(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    
    public function getInvoiceById(int $id): ?Invoice;
    
    public function createInvoice(array $data): Invoice;
    
    public function updateInvoice(int $id, array $data): ?Invoice;
    
    public function deleteInvoice(int $id): bool;
    
    public function getRecentInvoices(int $limit = 10): array;
    
    public function calculateInvoiceTotal(array $items): float;
    
    public function updateInvoiceStatus(int $id, string $status): bool;
}