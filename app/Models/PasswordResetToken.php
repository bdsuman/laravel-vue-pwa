<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PasswordResetToken extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'otp',
        'ip_address',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return $this->verified_at === null && $this->expires_at->isFuture();
    }

    public function markAsVerified(): void
    {
        $this->update(['verified_at' => now()]);
    }

    public static function generateFor(string $email, ?string $ipAddress = null): self
    {
        // Invalidate existing tokens
        self::where('email', $email)
            ->whereNull('verified_at')
            ->update(['verified_at' => now()]);

        // Generate new OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'user_id' => User::where('email', $email)->first()?->id,
            'email' => $email,
            'otp' => $otp,
            'ip_address' => $ipAddress,
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
