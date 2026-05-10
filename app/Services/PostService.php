<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\PostDTO;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

final class PostService
{
    private Post $model;

    public function __construct(?Post $model = null)
    {
        $this->model = $model ?? new Post();
    }

    /**
     * Get paginated posts with filters
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('category', 'user');
        $cacheKey = 'posts_' . md5(json_encode($filters)) . '_page_' . request('page', 1);

        return Cache::remember($cacheKey, 300, function () use ($query, $filters) {
            if (!empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }

            if (!empty($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['search'])) {
                $query->where('title', 'like', '%' . $filters['search'] . '%');
            }

            return $query->latest()->paginate(15);
        });
    }

    /**
     * Create new post
     */
    public function create(PostDTO $dto): Post
    {
        $post = $this->model->create([
            'title' => $dto->title,
            'content' => $dto->content,
            'category_id' => $dto->category_id,
            'user_id' => auth()->id(),
            'status' => $dto->status ?? 'draft',
        ]);

        Cache::flush();

        return $post->load('category', 'user');
    }

    /**
     * Update post
     */
    public function update(int $id, PostDTO $dto): Post
    {
        $post = $this->model->findOrFail($id);
        
        $post->update([
            'title' => $dto->title,
            'content' => $dto->content,
            'category_id' => $dto->category_id,
            'status' => $dto->status ?? $post->status,
        ]);

        Cache::flush();

        return $post->fresh()->load('category', 'user');
    }

    /**
     * Delete post
     */
    public function delete(int $id): bool
    {
        $post = $this->model->findOrFail($id);
        
        Cache::flush();

        return $post->delete();
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?Post
    {
        return Cache::remember("post_{$id}", 300, function () use ($id) {
            return $this->model->with('category', 'user')->find($id);
        });
    }
}
