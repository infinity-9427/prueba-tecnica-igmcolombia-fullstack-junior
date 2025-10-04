<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class InvoicePdfService
{
    /**
     * The storage disk to use for PDF files
     */
    private string $storageDisk;

    public function __construct()
    {
        $this->storageDisk = config('filesystems.default', 'local');
    }

    /**
     * Generate PDF for an invoice and store it
     *
     * @param Invoice $invoice
     * @return string|null Returns the file path or null on failure
     */
    public function generatePdf(Invoice $invoice): ?string
    {
        try {
            // Load the invoice with its relationships
            $invoice->load(['client', 'items', 'user']);

            // Generate PDF content from blade view
            $pdf = Pdf::loadView('pdf.invoice', [
                'invoice' => $invoice
            ]);

            // Configure PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => public_path(),
            ]);

            // Generate filename
            $filename = $this->generateFilename($invoice);

            // Ensure the directory exists
            $this->ensureDirectoryExists();

            // Save PDF to storage
            $pdfContent = $pdf->output();
            $filePath = "invoices/{$filename}";
            
            if (Storage::disk($this->storageDisk)->put($filePath, $pdfContent)) {
                return $filePath;
            }

            return null;
        } catch (Exception $e) {
            // Log the error
            \Log::error('PDF generation failed for invoice ' . $invoice->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Generate a unique filename for the invoice PDF
     *
     * @param Invoice $invoice
     * @return string
     */
    private function generateFilename(Invoice $invoice): string
    {
        $invoiceNumber = Str::slug($invoice->invoice_number);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $uniqueId = Str::random(8);

        return "invoice_{$invoiceNumber}_{$timestamp}_{$uniqueId}.pdf";
    }

    /**
     * Ensure the invoices directory exists in storage
     *
     * @return void
     */
    private function ensureDirectoryExists(): void
    {
        if (!Storage::disk($this->storageDisk)->exists('invoices')) {
            Storage::disk($this->storageDisk)->makeDirectory('invoices');
        }
    }

    /**
     * Get the public URL for an invoice PDF
     *
     * @param string $filePath
     * @return string
     */
    public function getPdfUrl(string $filePath): string
    {
        return Storage::disk($this->storageDisk)->url($filePath);
    }

    /**
     * Delete an invoice PDF file
     *
     * @param string $filePath
     * @return bool
     */
    public function deletePdf(string $filePath): bool
    {
        try {
            if (Storage::disk($this->storageDisk)->exists($filePath)) {
                return Storage::disk($this->storageDisk)->delete($filePath);
            }
            return true; // File doesn't exist, consider it deleted
        } catch (Exception $e) {
            \Log::error('Failed to delete PDF file: ' . $filePath, [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if PDF file exists
     *
     * @param string $filePath
     * @return bool
     */
    public function pdfExists(string $filePath): bool
    {
        return Storage::disk($this->storageDisk)->exists($filePath);
    }

    /**
     * Get PDF file size in bytes
     *
     * @param string $filePath
     * @return int|false
     */
    public function getPdfSize(string $filePath): int|false
    {
        try {
            if ($this->pdfExists($filePath)) {
                return Storage::disk($this->storageDisk)->size($filePath);
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Regenerate PDF for an existing invoice
     *
     * @param Invoice $invoice
     * @return string|null
     */
    public function regeneratePdf(Invoice $invoice): ?string
    {
        // Delete old PDF if it exists
        if ($invoice->archivo_generado_path) {
            $this->deletePdf($invoice->archivo_generado_path);
        }

        // Generate new PDF
        $newPath = $this->generatePdf($invoice);

        if ($newPath) {
            // Update the invoice record
            $invoice->update(['archivo_generado_path' => $newPath]);
        }

        return $newPath;
    }

    /**
     * Download PDF as a response
     *
     * @param Invoice $invoice
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function downloadPdf(Invoice $invoice)
    {
        try {
            if (!$invoice->archivo_generado_path || !$this->pdfExists($invoice->archivo_generado_path)) {
                // Generate PDF if it doesn't exist
                $pdfPath = $this->generatePdf($invoice);
                if (!$pdfPath) {
                    return null;
                }
                $invoice->update(['archivo_generado_path' => $pdfPath]);
            }

            $filename = "Invoice_{$invoice->invoice_number}.pdf";
            return Storage::disk($this->storageDisk)->download($invoice->archivo_generado_path, $filename);
        } catch (Exception $e) {
            \Log::error('PDF download failed for invoice ' . $invoice->id, [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}