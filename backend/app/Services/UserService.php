<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService implements UserServiceInterface
{
    public function getAllUsers(int $perPage = 15): LengthAwarePaginator
    {
        return User::orderBy('name')
            ->paginate($perPage);
    }

    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function createUser(array $data): User
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            Log::info('User created', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'data' => array_merge($data, ['password' => '[HIDDEN]'])
            ]);
            throw $e;
        }
    }

    public function updateUser(int $id, array $data): ?User
    {
        $user = User::find($id);

        if (!$user) {
            return null;
        }

        try {
            $updateData = array_filter([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'role' => $data['role'] ?? $user->role,
            ]);

            if (isset($data['password']) && !empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            Log::info('User updated', [
                'user_id' => $id,
                'email' => $user->email
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to update user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'data' => array_merge($data, ['password' => '[HIDDEN]'])
            ]);
            throw $e;
        }
    }

    public function deleteUser(int $id): bool
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        try {
            $user->delete();

            Log::info('User deleted', [
                'user_id' => $id,
                'email' => $user->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function updateUserRole(int $id, string $role): bool
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        try {
            $oldRole = $user->role;
            $user->update(['role' => $role]);

            Log::info('User role updated', [
                'user_id' => $id,
                'email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $role
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update user role', [
                'user_id' => $id,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}