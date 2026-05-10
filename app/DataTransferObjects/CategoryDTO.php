<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

final class CategoryDTO extends Data
{
    public function __construct(
        #[Required, Max(100)]
        public readonly string $name,

        #[Required, Max(100), Unique('categories', 'slug')]
        public readonly string $slug,

        #[Nullable]
        public readonly ?string $description = null,

        #[Nullable]
        public readonly ?string $icon = null,

        #[Nullable]
        public readonly ?int $parent_id = null,

        #[Nullable]
        public readonly int $order = 0,

        #[Nullable]
        public readonly bool $is_active = true,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            slug: $request->input('slug'),
            description: $request->input('description'),
            icon: $request->input('icon'),
            parent_id: $request->input('parent_id'),
            order: (int) $request->input('order', 0),
            is_active: $request->boolean('is_active', true),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'] ?? null,
            icon: $data['icon'] ?? null,
            parent_id: $data['parent_id'] ?? null,
            order: (int) ($data['order'] ?? 0),
            is_active: $data['is_active'] ?? true,
        );
    }
}
