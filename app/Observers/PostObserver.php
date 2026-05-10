<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Support\Facades\Cache;

final class PostObserver
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $this->clearPostCache($post);
        
        activity()
            ->performedOn($post)
            ->withProperties([
                'title' => $post->title,
                'category_id' => $post->category_id,
            ])
            ->log('Post created');
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $this->clearPostCache($post);

        // Log only changed attributes
        $changes = $post->getChanges();
        unset($changes['updated_at']);

        activity()
            ->performedOn($post)
            ->withProperties($changes)
            ->log('Post updated');
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->clearPostCache($post);

        activity()
            ->performedOn($post)
            ->withProperties([
                'title' => $post->title,
            ])
            ->log('Post deleted');
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        $this->clearPostCache($post);

        activity()
            ->performedOn($post)
            ->log('Post restored');
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        $this->clearPostCache($post);
    }

    /**
     * Clear related cache
     */
    private function clearPostCache(Post $post): void
    {
        Cache::forget("post.{$post->id}");
    }
}
