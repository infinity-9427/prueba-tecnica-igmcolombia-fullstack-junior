<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function getAllUsers(int $perPage = 15): LengthAwarePaginator;
    
    public function getUserById(int $id): ?User;
    
    public function createUser(array $data): User;
    
    public function updateUser(int $id, array $data): ?User;
    
    public function deleteUser(int $id): bool;
    
    public function findUserByEmail(string $email): ?User;
    
    public function updateUserRole(int $id, string $role): bool;
}