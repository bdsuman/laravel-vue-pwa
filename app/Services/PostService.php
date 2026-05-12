<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\PostDTO;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class PostService
{
    private Post $model;

    public function __construct(?Post $model = null)
    {
        $this->model = $model ?? new Post();
    }

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
            $query->where('is_published', $filters['status'] === 'published');
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(PostDTO $dto): Post
    {
        $post = $this->model->create([
            'title' => $dto->title,
            'content' => $dto->content,
            'slug' => \Illuminate\Support\Str::slug($dto->title),
            'category_id' => $dto->category_id,
            'user_id' => Auth::id(),
            'excerpt' => $dto->excerpt,
            'featured_image' => $dto->featured_image,
            'is_published' => $dto->is_published ?? false,
        ]);

        return $post->load('category', 'user');
    }

    public function update(Post $post, PostDTO $dto): Post
    {
        $updateData = [];
        
        if ($dto->title !== null) {
            $updateData['title'] = $dto->title;
            $updateData['slug'] = \Illuminate\Support\Str::slug($dto->title);
        }
        if ($dto->content !== null) {
            $updateData['content'] = $dto->content;
        }
        if ($dto->category_id !== null) {
            $updateData['category_id'] = $dto->category_id;
        }
        if ($dto->is_published !== null) {
            $updateData['is_published'] = $dto->is_published;
        }
        if ($dto->excerpt !== null) {
            $updateData['excerpt'] = $dto->excerpt;
        }
        if ($dto->featured_image !== null) {
            $updateData['featured_image'] = $dto->featured_image;
        }

        $post->update($updateData);

        return $post->fresh()->load('category', 'user');
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }

    public function publish(Post $post): Post
    {
        $post->update(['is_published' => true]);
        return $post->fresh()->load('category', 'user');
    }

    public function unpublish(Post $post): Post
    {
        $post->update(['is_published' => false]);
        return $post->fresh()->load('category', 'user');
    }

    public function findById(int $id): ?Post
    {
        return $this->model->with('category', 'user')->find($id);
    }
}
