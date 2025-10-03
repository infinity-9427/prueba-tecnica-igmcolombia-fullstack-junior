<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Interfaces\ClientServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        private ClientServiceInterface $clientService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $clients = $this->clientService->getAllClients($perPage);

        return response()->json($clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->createClient($request->validated());

            return response()->json([
                'message' => 'Client created successfully',
                'client' => $client
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $client = $this->clientService->getClientById($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }

        return response()->json([
            'client' => $client
        ]);
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientService->updateClient($id, $request->validated());

            if (!$client) {
                return response()->json([
                    'message' => 'Client not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Client updated successfully',
                'client' => $client
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->clientService->deleteClient($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Client not found or cannot be deleted'
            ], 404);
        }

        return response()->json([
            'message' => 'Client deleted successfully'
        ]);
    }
}