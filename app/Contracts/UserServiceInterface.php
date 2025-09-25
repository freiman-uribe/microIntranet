<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function createUser(array $data): User;
    public function updateUser(int $id, array $data): User;
    public function deleteUser(int $id): bool;
    public function getUserById(int $id): ?User;
    public function getUserByEmail(string $email): ?User;
    public function getAllUsers(): \Illuminate\Database\Eloquent\Collection;
    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator;
    public function authenticateUser(string $email, string $password): ?User;
    public function getUserByToken(string $token): ?User;
}