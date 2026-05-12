<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        return $this->model->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'avatar' => $dto->avatar,
            'is_active' => $dto->is_active,
        ]);
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

        if ($dto->avatar !== null) {
            $data['avatar'] = $dto->avatar;
        }

        $user->update($data);

        return $user->fresh();
    }

    /**
     * Get paginated users list
     */
    public function getPaginated(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        $user = $this->model->findOrFail($id);
        return $user->delete();
    }
}
