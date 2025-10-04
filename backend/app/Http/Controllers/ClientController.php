<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Interfaces\ClientServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function __construct(
        private ClientServiceInterface $clientService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 100); // Limit to 100 max
            $clients = $this->clientService->getAllClients($perPage);

            Log::info('Clients list retrieved', [
                'user_id' => $request->user()->id,
                'per_page' => $perPage,
                'total_clients' => $clients->total(),
                'ip' => $request->ip()
            ]);

            return ApiResponseHelper::success(
                'Clients retrieved successfully',
                $clients->items(),
                200,
                [
                    'pagination' => [
                        'per_page' => $perPage,
                        'current_page' => $clients->currentPage(),
                        'total' => $clients->total(),
                        'last_page' => $clients->lastPage(),
                        'from' => $clients->firstItem(),
                        'to' => $clients->lastItem()
                    ]
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'user_id' => $request->user()->id,
                    'per_page' => $perPage,
                    'ip' => $request->ip()
                ],
                'Failed to retrieve clients due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'user_id' => $request->user()->id,
                    'per_page' => $perPage,
                    'ip' => $request->ip()
                ],
                'Failed to retrieve clients due to an unexpected error'
            );
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            // Check for existing client with same email
            $existingClientByEmail = $this->clientService->findClientByEmail($request->email);
            if ($existingClientByEmail) {
                Log::warning('Client creation attempt with existing email', [
                    'email' => $request->email,
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Client creation failed - email already exists',
                    'error' => 'A client with this email address already exists in the system',
                    'error_code' => 'EMAIL_DUPLICATE',
                    'details' => [
                        'field' => 'email',
                        'value' => $request->email,
                        'timestamp' => now()->toISOString(),
                    ]
                ], 409);
            }

            // Check for existing client with same document number
            $existingClientByDocument = $this->clientService->findClientByDocument($request->document_number);
            if ($existingClientByDocument) {
                Log::warning('Client creation attempt with existing document number', [
                    'document_number' => $request->document_number,
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Client creation failed - document number already exists',
                    'error' => 'A client with this document number already exists in the system',
                    'error_code' => 'DOCUMENT_DUPLICATE',
                    'details' => [
                        'field' => 'document_number',
                        'value' => $request->document_number,
                        'timestamp' => now()->toISOString(),
                    ]
                ], 409);
            }

            $client = $this->clientService->createClient($request->validated());

            Log::info('Client created successfully', [
                'client_id' => $client->id,
                'user_id' => $request->user()->id,
                'client_email' => $client->email,
                'ip' => $request->ip()
            ]);

            return ApiResponseHelper::created(
                'Client created successfully',
                [
                    'client' => [
                        'id' => $client->id,
                        'first_name' => $client->first_name,
                        'last_name' => $client->last_name,
                        'email' => $client->email,
                        'document_type' => $client->document_type,
                        'document_number' => $client->document_number,
                        'phone' => $client->phone,
                        'created_at' => $client->created_at
                    ]
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'user_id' => $request->user()->id,
                    'data' => $request->validated(),
                    'ip' => $request->ip()
                ],
                'Failed to create client due to database error'
            );

        } catch (\InvalidArgumentException $e) {
            Log::error('Invalid data for client creation', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'data' => $request->validated(),
                'ip' => $request->ip()
            ]);

            return ApiResponseHelper::validationError(
                'Client creation failed - invalid data',
                ['general' => [$e->getMessage()]],
                [
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ]
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'user_id' => $request->user()->id,
                    'data' => $request->validated(),
                    'ip' => $request->ip()
                ],
                'Failed to create client due to an unexpected error'
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClientById($id);

            if (!$client) {
                Log::warning('Client not found', [
                    'client_id' => $id,
                    'user_id' => request()->user()->id ?? null,
                    'ip' => request()->ip()
                ]);

                return ApiResponseHelper::notFound('Client', $id, [
                    'user_id' => request()->user()->id ?? null,
                    'ip' => request()->ip()
                ]);
            }

            Log::info('Client details retrieved', [
                'client_id' => $id,
                'user_id' => request()->user()->id ?? null,
                'ip' => request()->ip()
            ]);

            return ApiResponseHelper::success(
                'Client retrieved successfully',
                [
                    'client' => [
                        'id' => $client->id,
                        'first_name' => $client->first_name,
                        'last_name' => $client->last_name,
                        'full_name' => $client->first_name . ' ' . $client->last_name,
                        'email' => $client->email,
                        'document_type' => $client->document_type,
                        'document_number' => $client->document_number,
                        'phone' => $client->phone,
                        'created_at' => $client->created_at,
                        'updated_at' => $client->updated_at
                    ]
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'client_id' => $id,
                    'user_id' => request()->user()->id ?? null,
                    'ip' => request()->ip()
                ],
                'Failed to retrieve client due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'client_id' => $id,
                    'user_id' => request()->user()->id ?? null,
                    'ip' => request()->ip()
                ],
                'Failed to retrieve client due to an unexpected error'
            );
        }
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientService->updateClient($id, $request->validated());

            if (!$client) {
                return ApiResponseHelper::notFound('Client', $id, [
                    'user_id' => $request->user()->id,
                    'ip' => $request->ip()
                ]);
            }

            Log::info('Client updated successfully', [
                'client_id' => $id,
                'user_id' => $request->user()->id,
                'ip' => $request->ip()
            ]);

            return ApiResponseHelper::success(
                'Client updated successfully',
                [
                    'client' => [
                        'id' => $client->id,
                        'first_name' => $client->first_name,
                        'last_name' => $client->last_name,
                        'email' => $client->email,
                        'document_type' => $client->document_type,
                        'document_number' => $client->document_number,
                        'phone' => $client->phone,
                        'updated_at' => $client->updated_at
                    ]
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'client_id' => $id,
                    'user_id' => $request->user()->id,
                    'data' => $request->validated(),
                    'ip' => $request->ip()
                ],
                'Failed to update client due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'client_id' => $id,
                    'user_id' => $request->user()->id,
                    'data' => $request->validated(),
                    'ip' => $request->ip()
                ],
                'Failed to update client due to an unexpected error'
            );
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->clientService->deleteClient($id);

            if (!$deleted) {
                return ApiResponseHelper::notFound('Client', $id, [
                    'user_id' => request()->user()->id ?? null,
                    'ip' => request()->ip()
                ]);
            }

            Log::info('Client deleted successfully', [
                'client_id' => $id,
                'user_id' => request()->user()->id ?? null,
                'ip' => request()->ip()
            ]);

            return ApiResponseHelper::deleted('Client deleted successfully');

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponseHelper::databaseError(
                $e,
                [
                    'client_id' => $id,
                    'user_id' => request()->user()->id ?? null,
                    'ip' => request()->ip()
                ],
                'Failed to delete client due to database error'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::unexpectedError(
                $e,
                [
                    'client_id' => $id,
                    'user_id' => request()->user()->id ?? null,
                    'ip' => request()->ip()
                ],
                'Failed to delete client due to an unexpected error'
            );
        }
    }
}