<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceStatusRequest;
use App\Interfaces\InvoiceServiceInterface;
use App\Services\InvoicePdfService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceServiceInterface $invoiceService,
        private InvoicePdfService $pdfService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'client_id', 'status', 'invoice_number', 
                'date_from', 'date_to', 'due_date_from', 'due_date_to',
                'sort_by', 'sort_direction'
            ]);
            
            $perPage = min($request->get('per_page', 15), 100);
            $invoices = $this->invoiceService->getAllInvoices($filters, $perPage, $request->user());

            Log::info('Invoices list retrieved', [
                'user_id' => $request->user()->id,
                'user_role' => $request->user()->role,
                'filters' => $filters,
                'per_page' => $perPage,
                'total_invoices' => $invoices->total(),
                'ip' => $request->ip()
            ]);

            return ApiResponseHelper::success(
                'Invoices retrieved successfully',
                $invoices->items(),
                200,
                [
                    'pagination' => [
                        'current_page' => $invoices->currentPage(),
                        'per_page' => $invoices->perPage(),
                        'total' => $invoices->total(),
                        'last_page' => $invoices->lastPage(),
                        'from' => $invoices->firstItem(),
                        'to' => $invoices->lastItem()
                    ],
                    'filters_applied' => array_filter($filters),
                    'user_role' => $request->user()->role
                ]
            );

        } catch (QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'user_id' => $request->user()->id,
                    'filters' => $filters ?? [],
                    'ip' => $request->ip()
                ],
                'Failed to retrieve invoices due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'user_id' => $request->user()->id,
                    'filters' => $filters ?? [],
                    'ip' => $request->ip()
                ],
                'Failed to retrieve invoices due to an unexpected error'
            );
        }
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;
            
            $invoice = $this->invoiceService->createInvoice($data);

            Log::info('Invoice created successfully', [
                'invoice_id' => $invoice->id,
                'user_id' => $request->user()->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount,
                'ip' => $request->ip()
            ]);

            // Refresh the invoice to get the updated PDF path from the observer
            $invoice->refresh();

            return ApiResponseHelper::created(
                'Invoice created successfully',
                [
                    'invoice' => [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'description' => $invoice->description,
                        'issue_date' => $invoice->issue_date,
                        'due_date' => $invoice->due_date,
                        'total_amount' => $invoice->total_amount,
                        'status' => $invoice->status,
                        'created_at' => $invoice->created_at,
                        'pdf_url' => $invoice->getPdfUrl(),
                        'has_pdf' => $invoice->hasPdf(),
                        'pdf_size' => $invoice->getPdfSize()
                    ]
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'user_id' => $request->user()->id,
                    'data' => $data ?? [],
                    'ip' => $request->ip()
                ],
                'Failed to create invoice due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'user_id' => $request->user()->id,
                    'data' => $data ?? [],
                    'ip' => $request->ip()
                ],
                'Failed to create invoice due to an unexpected error'
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id);

            if (!$invoice) {
                return ApiResponseHelper::notFound('Invoice', $id, [
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ]);
            }

            Log::info('Invoice details retrieved', [
                'invoice_id' => $id,
                'user_id' => request()->user()->id,
                'ip' => request()->ip()
            ]);

            return ApiResponseHelper::success(
                'Invoice retrieved successfully',
                [
                    'invoice' => [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'description' => $invoice->description,
                        'additional_notes' => $invoice->additional_notes,
                        'issue_date' => $invoice->issue_date,
                        'due_date' => $invoice->due_date,
                        'total_amount' => $invoice->total_amount,
                        'status' => $invoice->status,
                        'client' => $invoice->client,
                        'user' => $invoice->user,
                        'items' => $invoice->items,
                        'created_at' => $invoice->created_at,
                        'updated_at' => $invoice->updated_at
                    ]
                ]
            );

        } catch (QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to retrieve invoice due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to retrieve invoice due to an unexpected error'
            );
        }
    }

    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->updateInvoice($id, $request->validated());

            if (!$invoice) {
                return ApiResponseHelper::notFound('Invoice', $id, [
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ]);
            }

            Log::info('Invoice updated successfully', [
                'invoice_id' => $id,
                'user_id' => $request->user()->id,
                'ip' => $request->ip()
            ]);

            return ApiResponseHelper::success(
                'Invoice updated successfully',
                [
                    'invoice' => [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'description' => $invoice->description,
                        'issue_date' => $invoice->issue_date,
                        'due_date' => $invoice->due_date,
                        'total_amount' => $invoice->total_amount,
                        'status' => $invoice->status,
                        'updated_at' => $invoice->updated_at
                    ]
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => $request->user()->id,
                    'data' => $request->validated(),
                    'ip' => $request->ip()
                ],
                'Failed to update invoice due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => $request->user()->id,
                    'data' => $request->validated(),
                    'ip' => $request->ip()
                ],
                'Failed to update invoice due to an unexpected error'
            );
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->invoiceService->deleteInvoice($id);

            if (!$deleted) {
                return ApiResponseHelper::notFound('Invoice', $id, [
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ]);
            }

            Log::info('Invoice deleted successfully', [
                'invoice_id' => $id,
                'user_id' => request()->user()->id,
                'ip' => request()->ip()
            ]);

            return ApiResponseHelper::deleted('Invoice deleted successfully');

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to delete invoice due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to delete invoice due to an unexpected error'
            );
        }
    }

    public function updateStatus(UpdateInvoiceStatusRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->invoiceService->updateInvoiceStatus($id, $request->status);

            if (!$updated) {
                return ApiResponseHelper::notFound('Invoice', $id, [
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ]);
            }

            Log::info('Invoice status updated successfully', [
                'invoice_id' => $id,
                'new_status' => $request->status,
                'user_id' => $request->user()->id,
                'ip' => $request->ip()
            ]);

            return ApiResponseHelper::success(
                'Invoice status updated successfully',
                [
                    'invoice_id' => $id,
                    'status' => $request->status,
                    'updated_at' => now()->toISOString()
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'invoice_id' => $id,
                    'status' => $request->status,
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ],
                'Failed to update invoice status due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'invoice_id' => $id,
                    'status' => $request->status,
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ],
                'Failed to update invoice status due to an unexpected error'
            );
        }
    }

    public function recent(): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getRecentInvoices();

            Log::info('Recent invoices retrieved', [
                'user_id' => request()->user()->id,
                'invoice_count' => count($invoices),
                'ip' => request()->ip()
            ]);

            return ApiResponseHelper::success(
                'Recent invoices retrieved successfully',
                [
                    'invoices' => $invoices,
                    'count' => count($invoices)
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to retrieve recent invoices due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to retrieve recent invoices due to an unexpected error'
            );
        }
    }

    /**
     * Download invoice PDF
     */
    public function downloadPdf(int $id): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id, request()->user());

            if (!$invoice) {
                return ApiResponseHelper::notFound('Invoice', $id, [
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ]);
            }

            $response = $this->pdfService->downloadPdf($invoice);

            if (!$response) {
                return ApiResponseHelper::error(
                    'PDF file not found or could not be generated',
                    500,
                    [
                        'invoice_id' => $id,
                        'user_id' => request()->user()->id,
                        'ip' => request()->ip()
                    ]
                );
            }

            Log::info('Invoice PDF downloaded', [
                'invoice_id' => $id,
                'user_id' => request()->user()->id,
                'ip' => request()->ip()
            ]);

            return $response;

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to download invoice PDF'
            );
        }
    }

    /**
     * Regenerate invoice PDF
     */
    public function regeneratePdf(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id, request()->user());

            if (!$invoice) {
                return ApiResponseHelper::notFound('Invoice', $id, [
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ]);
            }

            $pdfPath = $this->pdfService->regeneratePdf($invoice);

            if (!$pdfPath) {
                return ApiResponseHelper::error(
                    'Failed to regenerate PDF',
                    500,
                    [
                        'invoice_id' => $id,
                        'user_id' => request()->user()->id,
                        'ip' => request()->ip()
                    ]
                );
            }

            Log::info('Invoice PDF regenerated', [
                'invoice_id' => $id,
                'user_id' => request()->user()->id,
                'pdf_path' => $pdfPath,
                'ip' => request()->ip()
            ]);

            // Refresh the invoice to get updated data
            $invoice->refresh();

            return ApiResponseHelper::success(
                'PDF regenerated successfully',
                [
                    'invoice_id' => $invoice->id,
                    'pdf_url' => $invoice->getPdfUrl(),
                    'has_pdf' => $invoice->hasPdf(),
                    'pdf_size' => $invoice->getPdfSize()
                ]
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to regenerate invoice PDF'
            );
        }
    }

    /**
     * Get invoice PDF info
     */
    public function getPdfInfo(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id, request()->user());

            if (!$invoice) {
                return ApiResponseHelper::notFound('Invoice', $id, [
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ]);
            }

            return ApiResponseHelper::success(
                'PDF information retrieved successfully',
                [
                    'invoice_id' => $invoice->id,
                    'pdf_url' => $invoice->getPdfUrl(),
                    'has_pdf' => $invoice->hasPdf(),
                    'pdf_size' => $invoice->getPdfSize(),
                    'pdf_path' => $invoice->archivo_generado_path
                ]
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'invoice_id' => $id,
                    'user_id' => request()->user()->id,
                    'ip' => request()->ip()
                ],
                'Failed to retrieve PDF information'
            );
        }
    }
}