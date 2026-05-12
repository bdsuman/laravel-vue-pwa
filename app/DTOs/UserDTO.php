<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $avatar = null,
        public ?bool $is_active = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            avatar: $data['avatar'] ?? null,
            is_active: $data['is_active'] ?? true,
        );
    }
}
