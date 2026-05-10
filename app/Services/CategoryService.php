<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

final class CategoryService
{
    private Category $model;

    public function __construct(?Category $model = null)
    {
        $this->model = $model ?? new Category();
    }

    /**
     * Get paginated categories
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Cache::remember('categories_list', 300, function () {
            return $this->model->withCount('posts')->latest()->paginate(15);
        });
    }

    /**
     * Get all categories (cached)
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('categories_all', 600, function () {
            return $this->model->orderBy('name')->get();
        });
    }

    /**
     * Create new category
     */
    public function create(CategoryDTO $dto): Category
    {
        $category = $this->model->create([
            'name' => $dto->name,
            'slug' => \Illuminate\Support\Str::slug($dto->name),
            'description' => $dto->description,
        ]);

        Cache::forget('categories_list');
        Cache::forget('categories_all');

        return $category;
    }

    /**
     * Update category
     */
    public function update(int $id, CategoryDTO $dto): Category
    {
        $category = $this->model->findOrFail($id);
        
        $category->update([
            'name' => $dto->name,
            'slug' => \Illuminate\Support\Str::slug($dto->name),
            'description' => $dto->description,
        ]);

        Cache::forget('categories_list');
        Cache::forget('categories_all');
        Cache::forget("category_{$id}");

        return $category->fresh();
    }

    /**
     * Delete category
     */
    public function delete(int $id): bool
    {
        $category = $this->model->findOrFail($id);
        
        Cache::forget('categories_list');
        Cache::forget('categories_all');
        Cache::forget("category_{$id}");

        return $category->delete();
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?Category
    {
        return Cache::remember("category_{$id}", 300, function () use ($id) {
            return $this->model->find($id);
        });
    }
}
