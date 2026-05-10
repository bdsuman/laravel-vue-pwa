<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\In;

final class PaginationDTO extends Data
{
    public function __construct(
        #[Required]
        public readonly int $per_page = 15,

        #[Required, In('asc,desc')]
        public readonly string $sort_by = 'created_at',

        #[Required, In('asc,desc')]
        public readonly string $sort_order = 'desc',

        public readonly ?int $page = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            per_page: min((int) $request->input('per_page', 15), 100),
            sort_by: $request->input('sort_by', 'created_at'),
            sort_order: $request->input('sort_order', 'desc'),
            page: $request->input('page') ? (int) $request->input('page') : null,
        );
    }

    public function toPageArray(): array
    {
        return [
            'per_page' => $this->per_page,
            'sort_by' => $this->sort_by,
            'sort_order' => $this->sort_order,
            'page' => $this->page ?? 1,
        ];
    }
}
