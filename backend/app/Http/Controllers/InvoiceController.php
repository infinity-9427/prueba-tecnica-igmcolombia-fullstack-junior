<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceStatusRequest;
use App\Interfaces\InvoiceServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceServiceInterface $invoiceService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'client_id', 'status', 'invoice_number', 
            'date_from', 'date_to', 'due_date_from', 'due_date_to',
            'sort_by', 'sort_direction'
        ]);
        
        $perPage = $request->get('per_page', 15);
        $invoices = $this->invoiceService->getAllInvoices($filters, $perPage);

        return response()->json($invoices);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;
            
            $invoice = $this->invoiceService->createInvoice($data);

            return response()->json([
                'message' => 'Invoice created successfully',
                'invoice' => $invoice
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->getInvoiceById($id);

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found'
            ], 404);
        }

        return response()->json([
            'invoice' => $invoice
        ]);
    }

    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->updateInvoice($id, $request->validated());

            if (!$invoice) {
                return response()->json([
                    'message' => 'Invoice not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Invoice updated successfully',
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->invoiceService->deleteInvoice($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Invoice not found or cannot be deleted'
            ], 404);
        }

        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }

    public function updateStatus(UpdateInvoiceStatusRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->invoiceService->updateInvoiceStatus($id, $request->status);

            if (!$updated) {
                return response()->json([
                    'message' => 'Invoice not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Invoice status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update invoice status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recent(): JsonResponse
    {
        $invoices = $this->invoiceService->getRecentInvoices();

        return response()->json([
            'invoices' => $invoices
        ]);
    }
}