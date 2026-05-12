<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

final class PostDTO
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $content = null,
        public readonly ?int $category_id = null,
        public readonly ?string $excerpt = null,
        public readonly ?string $featured_image = null,
        public readonly ?bool $is_published = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            content: $request->input('content'),
            category_id: $request->input('category_id'),
            excerpt: $request->input('excerpt'),
            featured_image: $request->input('featured_image'),
            is_published: $request->has('is_published') 
                ? $request->boolean('is_published') 
                : null,
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
            is_published: $data['is_published'] ?? null,
        );
    }
}
