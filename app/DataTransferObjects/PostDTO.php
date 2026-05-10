<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;
use Carbon\Carbon;

final class PostDTO extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public readonly string $title,

        #[Required]
        public readonly string $content,

        #[Required, Exists('categories', 'id')]
        public readonly int $category_id,

        #[Nullable]
        public readonly ?string $excerpt = null,

        #[Nullable]
        public readonly ?string $featured_image = null,

        #[Nullable]
        public readonly bool $is_published = true,

        #[Nullable]
        public readonly ?Carbon $published_at = null,

        public readonly ?int $author_id = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            content: $request->input('content'),
            category_id: (int) $request->input('category_id'),
            excerpt: $request->input('excerpt'),
            featured_image: $request->input('featured_image'),
            is_published: $request->boolean('is_published', true),
            published_at: $request->has('published_at') 
                ? Carbon::parse($request->input('published_at')) 
                : null,
            author_id: $request->input('author_id'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            content: $data['content'],
            category_id: $data['category_id'],
            excerpt: $data['excerpt'] ?? null,
            featured_image: $data['featured_image'] ?? null,
            is_published: $data['is_published'] ?? true,
            published_at: isset($data['published_at']) ? Carbon::parse($data['published_at']) : null,
            author_id: $data['author_id'] ?? null,
        );
    }
}
