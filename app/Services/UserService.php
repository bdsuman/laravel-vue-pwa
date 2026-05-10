<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

final class UserService
{
    private User $model;

    public function __construct(?User $model = null)
    {
        $this->model = $model ?? new User();
    }

    /**
     * Create a new user from DTO
     */
    public function create(UserDTO $dto): User
    {
        $user = $this->model->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        // Clear cache
        Cache::forget('users_list');

        return $user;
    }

    /**
     * Update existing user
     */
    public function update(int $id, UserDTO $dto): User
    {
        $user = $this->model->findOrFail($id);
        
        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
        ];

        if ($dto->password) {
            $data['password'] = Hash::make($dto->password);
        }

        $user->update($data);

        // Clear cache
        Cache::forget('users_list');
        Cache::forget("user_{$id}");

        return $user->fresh();
    }

    /**
     * Get paginated users list with caching
     */
    public function getPaginated(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Cache::remember('users_list', 300, function () {
            return $this->model->latest()->paginate(15);
        });
    }

    /**
     * Find user by ID with caching
     */
    public function findById(int $id): ?User
    {
        return Cache::remember("user_{$id}", 300, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        $user = $this->model->findOrFail($id);
        
        // Clear cache
        Cache::forget('users_list');
        Cache::forget("user_{$id}");

        return $user->delete();
    }
}
