<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class CategoryService
{
    private Category $model;

    public function __construct(?Category $model = null)
    {
        $this->model = $model ?? new Category();
    }

    public function getAll(int $perPage = 15, ?string $search = null, bool $activeOnly = false): LengthAwarePaginator
    {
        $query = $this->model->query();

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('order')->latest()->paginate($perPage);
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
    }

    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }

    public function create(array $data): Category
    {
        $category = $this->model->create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'order' => $data['order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $category;
    }

    public function update(int $id, array $data): ?Category
    {
        $category = $this->model->find($id);

        if (!$category) {
            return null;
        }

        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
            $updateData['slug'] = $data['slug'] ?? Str::slug($data['name']);
        }

        if (array_key_exists('description', $data)) {
            $updateData['description'] = $data['description'];
        }

        if (array_key_exists('icon', $data)) {
            $updateData['icon'] = $data['icon'];
        }

        if (array_key_exists('parent_id', $data)) {
            $updateData['parent_id'] = $data['parent_id'];
        }

        if (array_key_exists('order', $data)) {
            $updateData['order'] = $data['order'];
        }

        if (array_key_exists('is_active', $data)) {
            $updateData['is_active'] = $data['is_active'];
        }

        $category->update($updateData);

        return $category->fresh();
    }

    public function delete(int $id): bool
    {
        $category = $this->model->find($id);

        if (!$category) {
            return false;
        }

        return $category->delete();
    }

    public function toggleStatus(int $id): ?Category
    {
        $category = $this->model->find($id);

        if (!$category) {
            return null;
        }

        $category->update(['is_active' => !$category->is_active]);

        return $category->fresh();
    }
}
