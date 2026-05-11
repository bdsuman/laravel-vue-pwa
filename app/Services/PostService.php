<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\PostDTO;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

        return $query->latest()->paginate($perPage);
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

        return $post->load('category', 'user');
    }

    /**
     * Update post
     */
    public function update(Post $post, PostDTO $dto): Post
    {
        $post->update([
            'title' => $dto->title,
            'content' => $dto->content,
            'category_id' => $dto->category_id,
            'status' => $dto->status ?? $post->status,
        ]);

        return $post->fresh()->load('category', 'user');
    }

    /**
     * Delete post
     */
    public function delete(Post $post): bool
    {
        return $post->delete();
    }

    /**
     * Publish post
     */
    public function publish(Post $post): Post
    {
        $post->update(['is_published' => true]);
        return $post->fresh()->load('category', 'user');
    }

    /**
     * Unpublish post
     */
    public function unpublish(Post $post): Post
    {
        $post->update(['is_published' => false]);
        return $post->fresh()->load('category', 'user');
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?Post
    {
        return $this->model->with('category', 'user')->find($id);
    }
}
