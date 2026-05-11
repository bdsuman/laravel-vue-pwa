<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \App\Models\Category
 */
final class CategoryBuilder extends Builder
{
    /**
     * Filter by active status
     */
    public function active(): self
    {
        return $this->where('is_active', true);
    }

    /**
     * Filter by parent category
     */
    public function parent(int $parentId): self
    {
        return $this->where('parent_id', $parentId);
    }

    /**
     * Filter by root categories (no parent)
     */
    public function root(): self
    {
        return $this->whereNull('parent_id');
    }

    /**
     * Search by name or slug
     */
    public function search(string $term): self
    {
        return $this->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                  ->orWhere('slug', 'like', "%{$term}%");
        });
    }

    /**
     * With children
     */
    public function withChildren(): self
    {
        return $this->with('children');
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
