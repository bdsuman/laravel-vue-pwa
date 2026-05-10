<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \App\Models\Post
 */
final class PostBuilder extends Builder
{
    /**
     * Filter by published status
     */
    public function published(): self
    {
        return $this->where('is_published', true);
    }

    /**
     * Filter by unpublished posts
     */
    public function unpublished(): self
    {
        return $this->where('is_published', false);
    }

    /**
     * Filter by category
     */
    public function forCategory(int $categoryId): self
    {
        return $this->where('category_id', $categoryId);
    }

    /**
     * Filter by author
     */
    public function byAuthor(int $authorId): self
    {
        return $this->where('author_id', $authorId);
    }

    /**
     * Search in title and content
     */
    public function search(string $term): self
    {
        return $this->where(function (Builder $query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /**
     * Order by latest
     */
    public function latest(): self
    {
        return $this->orderBy('created_at', 'desc');
    }

    /**
     * Order by oldest
     */
    public function oldest(): self
    {
        return $this->orderBy('created_at', 'asc');
    }

    /**
     * With related data
     */
    public function withRelations(): self
    {
        return $this->with(['category', 'author']);
    }

    /**
     * Filter by date range
     */
    public function betweenDates(
        \DateTimeInterface $start,
        \DateTimeInterface $end
    ): self {
        return $this->whereBetween('created_at', [
            $start->format('Y-m-d H:i:s'),
            $end->format('Y-m-d H:i:s'),
        ]);
    }
}
