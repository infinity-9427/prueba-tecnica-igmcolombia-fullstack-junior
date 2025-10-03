<?php

namespace App\Interfaces;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClientServiceInterface
{
    public function getAllClients(int $perPage = 15): LengthAwarePaginator;
    
    public function getClientById(int $id): ?Client;
    
    public function createClient(array $data): Client;
    
    public function updateClient(int $id, array $data): ?Client;
    
    public function deleteClient(int $id): bool;
    
    public function findClientByEmail(string $email): ?Client;
    
    public function findClientByDocument(string $documentNumber): ?Client;
}