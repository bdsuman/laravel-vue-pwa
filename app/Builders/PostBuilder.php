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
     * Filter by draft status
     */
    public function draft(): self
    {
        return $this->where('is_published', false);
    }

    /**
     * Filter by featured status
     */
    public function featured(): self
    {
        return $this->where('is_featured', true);
    }

    /**
     * Filter by category
     */
    public function category(string|int $categorySlugOrId): self
    {
        return $this->where('category_id', $categorySlugOrId);
    }

    /**
     * Search by title or content
     */
    public function search(string $term): self
    {
        return $this->where(function (Builder $query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /**
     * With relationships
     */
    public function withRelations(): self
    {
        return $this->with(['category', 'author', 'tags']);
    }

    /**
     * Order by latest
     */
    public function latest($column = null): self
    {
        return parent::latest($column);
    }

    /**
     * Order by oldest
     */
    public function oldest($column = null): self
    {
        return parent::oldest($column);
    }
}
