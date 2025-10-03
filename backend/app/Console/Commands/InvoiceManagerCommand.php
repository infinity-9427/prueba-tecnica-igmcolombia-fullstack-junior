<?php

namespace App\Console\Commands;

use App\Interfaces\InvoiceServiceInterface;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InvoiceManagerCommand extends Command
{
    protected $signature = 'invoice:manage 
                           {action : Action to perform (list|show|update-status|check-overdue)}
                           {--id= : Invoice ID (required for show and update-status)}
                           {--status= : New status (required for update-status, values: pending|paid|overdue)}
                           {--filter-status= : Filter by status (for list action)}
                           {--filter-client= : Filter by client ID (for list action)}
                           {--limit=10 : Number of records to display (for list action)}';

    protected $description = 'Manage invoices from command line - list, show, update status, or check overdue invoices';

    public function __construct(
        private InvoiceServiceInterface $invoiceService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        try {
            switch ($action) {
                case 'list':
                    return $this->listInvoices();
                case 'show':
                    return $this->showInvoice();
                case 'update-status':
                    return $this->updateInvoiceStatus();
                case 'check-overdue':
                    return $this->checkOverdueInvoices();
                default:
                    $this->error("Invalid action: {$action}");
                    $this->info('Valid actions: list, show, update-status, check-overdue');
                    return 1;
            }
        } catch (\Exception $e) {
            $this->error("Command failed: {$e->getMessage()}");
            Log::error('Invoice command failed', [
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    private function listInvoices(): int
    {
        $filters = [];
        
        if ($this->option('filter-status')) {
            $filters['status'] = $this->option('filter-status');
        }
        
        if ($this->option('filter-client')) {
            $filters['client_id'] = $this->option('filter-client');
        }

        $limit = (int) $this->option('limit');
        $invoices = $this->invoiceService->getAllInvoices($filters, $limit);

        if ($invoices->isEmpty()) {
            $this->info('No invoices found.');
            return 0;
        }

        $this->info("Found {$invoices->total()} invoice(s):");
        $this->newLine();

        $headers = ['ID', 'Number', 'Client', 'Status', 'Total', 'Issue Date', 'Due Date'];
        $rows = [];

        foreach ($invoices->items() as $invoice) {
            $rows[] = [
                $invoice->id,
                $invoice->invoice_number,
                $invoice->client->full_name ?? 'N/A',
                ucfirst($invoice->status),
                '$' . number_format($invoice->total_amount, 2),
                $invoice->issue_date->format('Y-m-d'),
                $invoice->due_date->format('Y-m-d'),
            ];
        }

        $this->table($headers, $rows);

        Log::info('Invoices listed via command', [
            'count' => $invoices->count(),
            'filters' => $filters
        ]);

        return 0;
    }

    private function showInvoice(): int
    {
        $id = $this->option('id');

        if (!$id) {
            $this->error('Invoice ID is required for show action. Use --id=123');
            return 1;
        }

        $invoice = $this->invoiceService->getInvoiceById($id);

        if (!$invoice) {
            $this->error("Invoice with ID {$id} not found.");
            return 1;
        }

        $this->info("Invoice Details:");
        $this->newLine();

        $this->line("ID: {$invoice->id}");
        $this->line("Number: {$invoice->invoice_number}");
        $this->line("Client: {$invoice->client->full_name}");
        $this->line("User: {$invoice->user->name}");
        $this->line("Status: " . ucfirst($invoice->status));
        $this->line("Total Amount: $" . number_format($invoice->total_amount, 2));
        $this->line("Issue Date: {$invoice->issue_date->format('Y-m-d')}");
        $this->line("Due Date: {$invoice->due_date->format('Y-m-d')}");
        
        if ($invoice->description) {
            $this->line("Description: {$invoice->description}");
        }

        if ($invoice->additional_notes) {
            $this->line("Notes: {$invoice->additional_notes}");
        }

        if ($invoice->items->isNotEmpty()) {
            $this->newLine();
            $this->info("Items:");
            
            $itemHeaders = ['Name', 'Quantity', 'Unit Price', 'Tax Rate', 'Total'];
            $itemRows = [];

            foreach ($invoice->items as $item) {
                $itemRows[] = [
                    $item->name,
                    $item->quantity,
                    '$' . number_format($item->unit_price, 2),
                    $item->tax_rate . '%',
                    '$' . number_format($item->total_amount, 2),
                ];
            }

            $this->table($itemHeaders, $itemRows);
        }

        return 0;
    }

    private function updateInvoiceStatus(): int
    {
        $id = $this->option('id');
        $status = $this->option('status');

        if (!$id) {
            $this->error('Invoice ID is required for update-status action. Use --id=123');
            return 1;
        }

        if (!$status) {
            $this->error('Status is required for update-status action. Use --status=paid');
            return 1;
        }

        if (!in_array($status, ['pending', 'paid', 'overdue'])) {
            $this->error('Invalid status. Valid values: pending, paid, overdue');
            return 1;
        }

        $updated = $this->invoiceService->updateInvoiceStatus($id, $status);

        if (!$updated) {
            $this->error("Invoice with ID {$id} not found or could not be updated.");
            return 1;
        }

        $this->info("Invoice {$id} status updated to '{$status}' successfully.");

        Log::info('Invoice status updated via command', [
            'invoice_id' => $id,
            'new_status' => $status
        ]);

        return 0;
    }

    private function checkOverdueInvoices(): int
    {
        $overdueInvoices = Invoice::with(['client', 'user'])
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->info('No overdue invoices found.');
            return 0;
        }

        $this->warn("Found {$overdueInvoices->count()} overdue invoice(s):");
        $this->newLine();

        $headers = ['ID', 'Number', 'Client', 'Total', 'Due Date', 'Days Overdue'];
        $rows = [];

        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date);
            
            $rows[] = [
                $invoice->id,
                $invoice->invoice_number,
                $invoice->client->full_name ?? 'N/A',
                '$' . number_format($invoice->total_amount, 2),
                $invoice->due_date->format('Y-m-d'),
                $daysOverdue,
            ];

            $this->invoiceService->updateInvoiceStatus($invoice->id, 'overdue');
        }

        $this->table($headers, $rows);

        $this->info("Updated {$overdueInvoices->count()} invoice(s) to overdue status.");

        Log::info('Overdue invoices checked and updated via command', [
            'count' => $overdueInvoices->count()
        ]);

        return 0;
    }
}