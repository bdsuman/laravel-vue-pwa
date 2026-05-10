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
     * Filter by inactive status
     */
    public function inactive(): self
    {
        return $this->where('is_active', false);
    }

    /**
     * Filter by parent category
     */
    public function childrenOf(int $parentId): self
    {
        return $this->where('parent_id', $parentId);
    }

    /**
     * Filter root categories only
     */
    public function rootOnly(): self
    {
        return $this->whereNull('parent_id');
    }

    /**
     * Order by display order
     */
    public function ordered(): self
    {
        return $this->orderBy('order')->orderBy('name');
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
     * With parent relationship
     */
    public function withParent(): self
    {
        return $this->with('parent');
    }

    /**
     * With children relationship
     */
    public function withChildren(): self
    {
        return $this->with('children');
    }
}
