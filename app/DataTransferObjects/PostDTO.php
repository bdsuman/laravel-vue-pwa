<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;
use Carbon\Carbon;

final class PostDTO extends Data
{
    public function __construct(
        #[Nullable]
        public readonly ?string $title = null,

        #[Nullable]
        public readonly ?string $content = null,

        #[Nullable]
        public readonly ?int $category_id = null,

        #[Nullable]
        public readonly ?string $excerpt = null,

        #[Nullable]
        public readonly ?string $featured_image = null,

        #[Nullable]
        public readonly ?int $user_id = null,

        #[Nullable]
        public readonly ?bool $is_published = true,

        #[Nullable]
        public readonly ?Carbon $published_at = null,

        #[Nullable]
        public readonly ?string $meta_title = null,

        #[Nullable]
        public readonly ?string $meta_description = null,

        #[Nullable]
        public readonly ?array $tags = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            content: $request->input('content'),
            category_id: $request->has('category_id') ? (int) $request->input('category_id') : null,
            excerpt: $request->input('excerpt'),
            featured_image: $request->input('featured_image'),
            user_id: $request->input('user_id'),
            is_published: $request->has('is_published') ? $request->boolean('is_published') : true,
            published_at: $request->has('published_at') 
                ? Carbon::parse($request->input('published_at')) 
                : null,
            meta_title: $request->input('meta_title'),
            meta_description: $request->input('meta_description'),
            tags: $request->input('tags'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            content: $data['content'] ?? null,
            category_id: $data['category_id'] ?? null,
            excerpt: $data['excerpt'] ?? null,
            featured_image: $data['featured_image'] ?? null,
            user_id: $data['user_id'] ?? null,
            is_published: $data['is_published'] ?? true,
            published_at: isset($data['published_at']) ? Carbon::parse($data['published_at']) : null,
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            tags: $data['tags'] ?? null,
        );
    }
}
