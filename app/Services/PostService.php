<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\PostDTO;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class PostService
{
    private Post $model;

    public function __construct(?Post $model = null)
    {
        $this->model = $model ?? new Post();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['category', 'user']);

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'published') {
                $query->published();
            } elseif ($filters['status'] === 'draft') {
                $query->where('is_published', false);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAll(): Collection
    {
        return $this->model->with(['category', 'user'])->latest()->get();
    }

    public function findById(int $id): ?Post
    {
        return $this->model->with(['category', 'user'])->find($id);
    }

    public function create(PostDTO $dto): Post
    {
        $post = $this->model->create([
            'title' => $dto->title,
            'slug' => Str::slug($dto->title ?? 'post') . '-' . Str::random(6),
            'content' => $dto->content,
            'excerpt' => $dto->excerpt,
            'category_id' => $dto->category_id,
            'user_id' => $dto->user_id,
            'featured_image' => $dto->featured_image,
            'is_published' => $dto->is_published,
            'published_at' => $dto->published_at,
            'meta_title' => $dto->meta_title,
            'meta_description' => $dto->meta_description,
            'tags' => $dto->tags,
        ]);

        return $post->load(['category', 'user']);
    }

    public function update(Post $post, PostDTO $dto): Post
    {
        $updateData = [];

        if (isset($dto->title)) {
            $updateData['title'] = $dto->title;
            $updateData['slug'] = Str::slug($dto->title) . '-' . Str::random(6);
        }

        if ($dto->content !== null) {
            $updateData['content'] = $dto->content;
        }

        if ($dto->excerpt !== null) {
            $updateData['excerpt'] = $dto->excerpt;
        }

        if ($dto->category_id !== null) {
            $updateData['category_id'] = $dto->category_id;
        }

        if ($dto->featured_image !== null) {
            $updateData['featured_image'] = $dto->featured_image;
        }

        if ($dto->is_published !== null) {
            $updateData['is_published'] = $dto->is_published;
        }

        if ($dto->published_at !== null) {
            $updateData['published_at'] = $dto->published_at;
        }

        if ($dto->meta_title !== null) {
            $updateData['meta_title'] = $dto->meta_title;
        }

        if ($dto->meta_description !== null) {
            $updateData['meta_description'] = $dto->meta_description;
        }

        if ($dto->tags !== null) {
            $updateData['tags'] = $dto->tags;
        }

        $post->update($updateData);

        return $post->load(['category', 'user']);
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }

    public function publish(Post $post): Post
    {
        $post->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return $post;
    }
}
