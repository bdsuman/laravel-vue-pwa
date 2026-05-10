<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Password;

final class UserDTO extends Data
{
    public function __construct(
        #[Min(2)]
        public readonly string $name,

        #[Email, Unique('users', 'email')]
        public readonly string $email,

        #[Password, Min(8), Confirmed]
        public readonly string $password,

        public readonly ?string $avatar = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            avatar: $request->input('avatar'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? '',
            avatar: $data['avatar'] ?? null,
        );
    }
}
