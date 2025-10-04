<?php

namespace App\Services;

use App\Interfaces\ClientServiceInterface;
use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ClientService implements ClientServiceInterface
{
    public function getAllClients(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return Client::orderBy('first_name')
                ->orderBy('last_name')
                ->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve clients', [
                'error' => $e->getMessage(),
                'per_page' => $perPage
            ]);
            throw $e;
        }
    }

    public function getClientById(int $id): ?Client
    {
        try {
            return Client::find($id);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve client by ID', [
                'client_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function createClient(array $data): Client
    {
        try {
            $client = Client::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'document_type' => $data['document_type'],
                'document_number' => $data['document_number'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);

            Log::info('Client created', [
                'client_id' => $client->id,
                'email' => $client->email,
                'document_number' => $client->document_number
            ]);

            return $client;
        } catch (\Exception $e) {
            Log::error('Failed to create client', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateClient(int $id, array $data): ?Client
    {
        $client = Client::find($id);

        if (!$client) {
            return null;
        }

        try {
            $updateData = array_filter([
                'first_name' => $data['first_name'] ?? $client->first_name,
                'last_name' => $data['last_name'] ?? $client->last_name,
                'document_type' => $data['document_type'] ?? $client->document_type,
                'document_number' => $data['document_number'] ?? $client->document_number,
                'email' => $data['email'] ?? $client->email,
                'phone' => $data['phone'] ?? $client->phone,
            ]);

            $client->update($updateData);

            Log::info('Client updated', [
                'client_id' => $id,
                'email' => $client->email
            ]);

            return $client;
        } catch (\Exception $e) {
            Log::error('Failed to update client', [
                'client_id' => $id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function deleteClient(int $id): bool
    {
        $client = Client::find($id);

        if (!$client) {
            return false;
        }

        try {
            $client->delete();

            Log::info('Client deleted', [
                'client_id' => $id,
                'email' => $client->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete client', [
                'client_id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function findClientByEmail(string $email): ?Client
    {
        try {
            return Client::where('email', $email)->first();
        } catch (\Exception $e) {
            Log::error('Failed to find client by email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function findClientByDocument(string $documentNumber): ?Client
    {
        try {
            return Client::where('document_number', $documentNumber)->first();
        } catch (\Exception $e) {
            Log::error('Failed to find client by document', [
                'document_number' => $documentNumber,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}