<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class CategoryService
{
    private Category $model;

    public function __construct(?Category $model = null)
    {
        $this->model = $model ?? new Category();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->withCount('posts')->latest()->paginate($perPage);
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function getWithHierarchy(): Collection
    {
        return $this->model->with(['parent', 'children'])->whereNull('parent_id')->get();
    }

    public function create(array $data): Category
    {
        $category = $this->model->create([
            'name' => $data['name'],
            'slug' => \Illuminate\Support\Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $updateData = [];
        
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
            $updateData['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        if (array_key_exists('description', $data)) {
            $updateData['description'] = $data['description'];
        }
        if (array_key_exists('parent_id', $data)) {
            $updateData['parent_id'] = $data['parent_id'];
        }
        if (isset($data['is_active'])) {
            $updateData['is_active'] = $data['is_active'];
        }

        $category->update($updateData);

        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function toggleActive(Category $category): Category
    {
        $category->update(['is_active' => !$category->is_active]);
        return $category->fresh();
    }

    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }
}
