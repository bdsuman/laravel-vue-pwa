<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

final class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        $this->clearCategoryCache();

        activity()
            ->performedOn($category)
            ->withProperties([
                'name' => $category->name,
                'slug' => $category->slug,
            ])
            ->log('Category created');
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $this->clearCategoryCache();

        $changes = $category->getChanges();
        unset($changes['updated_at']);

        activity()
            ->performedOn($category)
            ->withProperties($changes)
            ->log('Category updated');
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $this->clearCategoryCache();

        activity()
            ->performedOn($category)
            ->withProperties([
                'name' => $category->name,
            ])
            ->log('Category deleted');
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        $this->clearCategoryCache();
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        $this->clearCategoryCache();
    }

    /**
     * Clear all category-related cache
     */
    private function clearCategoryCache(): void
    {
        Cache::forget('categories.active');
        Cache::forget('categories.hierarchy');
    }
}
