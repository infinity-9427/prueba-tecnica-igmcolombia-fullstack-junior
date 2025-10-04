<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    protected InvoicePdfService $pdfService;

    public function __construct(InvoicePdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        $this->generatePdfAsync($invoice, 'created');
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        // Only regenerate PDF if important fields have changed
        $importantFields = [
            'client_id',
            'description',
            'additional_notes',
            'issue_date',
            'due_date',
            'total_amount',
            'status'
        ];

        $hasImportantChanges = false;
        foreach ($importantFields as $field) {
            if ($invoice->isDirty($field)) {
                $hasImportantChanges = true;
                break;
            }
        }

        // Also check if items were modified (this would require a separate check)
        // For now, we'll regenerate if any important field changed
        if ($hasImportantChanges) {
            $this->generatePdfAsync($invoice, 'updated');
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        // Clean up the PDF file when invoice is deleted
        if ($invoice->archivo_generado_path) {
            try {
                $this->pdfService->deletePdf($invoice->archivo_generado_path);
                Log::info("Deleted PDF for invoice {$invoice->id}", [
                    'invoice_id' => $invoice->id,
                    'pdf_path' => $invoice->archivo_generado_path
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to delete PDF for invoice {$invoice->id}", [
                    'invoice_id' => $invoice->id,
                    'pdf_path' => $invoice->archivo_generado_path,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Generate PDF asynchronously (or synchronously for now)
     * In a real application, you might want to dispatch this to a queue
     */
    private function generatePdfAsync(Invoice $invoice, string $event): void
    {
        try {
            // For now, we'll generate synchronously
            // In production, consider using a queue for better performance
            $this->generatePdfNow($invoice, $event);
            
            // To use queues, uncomment the following line and comment the above:
            // dispatch(new GenerateInvoicePdfJob($invoice, $event));
        } catch (\Exception $e) {
            Log::error("Failed to initiate PDF generation for invoice {$invoice->id}", [
                'invoice_id' => $invoice->id,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate PDF immediately
     */
    private function generatePdfNow(Invoice $invoice, string $event): void
    {
        try {
            Log::info("Starting PDF generation for invoice {$invoice->id}", [
                'invoice_id' => $invoice->id,
                'event' => $event,
                'invoice_number' => $invoice->invoice_number
            ]);

            // If this is an update and PDF already exists, delete the old one
            if ($event === 'updated' && $invoice->archivo_generado_path) {
                $this->pdfService->deletePdf($invoice->archivo_generado_path);
            }

            // Generate new PDF
            $pdfPath = $this->pdfService->generatePdf($invoice);

            if ($pdfPath) {
                // Update the invoice record with the PDF path
                // Use updateQuietly to avoid triggering the observer again
                $invoice->updateQuietly(['archivo_generado_path' => $pdfPath]);

                Log::info("Successfully generated PDF for invoice {$invoice->id}", [
                    'invoice_id' => $invoice->id,
                    'event' => $event,
                    'pdf_path' => $pdfPath,
                    'pdf_url' => $this->pdfService->getPdfUrl($pdfPath)
                ]);
            } else {
                Log::error("PDF generation failed for invoice {$invoice->id}", [
                    'invoice_id' => $invoice->id,
                    'event' => $event
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Exception during PDF generation for invoice {$invoice->id}", [
                'invoice_id' => $invoice->id,
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}