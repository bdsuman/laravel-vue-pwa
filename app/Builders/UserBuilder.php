<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \App\Models\User
 */
final class UserBuilder extends Builder
{
    /**
     * Filter by role
     */
    public function role(string $role): self
    {
        return $this->whereHas('roles', function (Builder $query) use ($role) {
            $query->where('name', $role);
        });
    }

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
     * Search by name or email
     */
    public function search(string $term): self
    {
        return $this->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
        });
    }

    /**
     * Filter by creation date
     */
    public function createdAfter(\DateTimeInterface $date): self
    {
        return $this->where('created_at', '>=', $date->format('Y-m-d H:i:s'));
    }

    /**
     * Order by latest
     */
    public function latest(): self
    {
        return $this->orderBy('created_at', 'desc');
    }
}
