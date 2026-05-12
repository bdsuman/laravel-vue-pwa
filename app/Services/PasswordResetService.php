<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

final class PasswordResetService
{
    private const COOLDOWN_SECONDS = 60;
    private const MAX_ATTEMPTS_PER_HOUR = 5;

    private function getRedisKey(string $email, string $type): string
    {
        return "password_reset:{$type}:{$email}";
    }

    private function checkCooldown(string $email): ?array
    {
        $cooldownKey = $this->getRedisKey($email, 'cooldown');
        $exists = Redis::connection()->exists($cooldownKey);

        if ($exists) {
            $remaining = Redis::connection()->ttl($cooldownKey);
            return [
                'success' => false,
                'message' => "Please wait {$remaining} seconds before requesting another OTP",
                'cooldown_remaining' => $remaining,
            ];
        }

        return null;
    }

    private function checkAttempts(string $email): ?array
    {
        $attemptsKey = $this->getRedisKey($email, 'attempts');
        $attempts = (int) Redis::connection()->get($attemptsKey);

        if ($attempts >= self::MAX_ATTEMPTS_PER_HOUR) {
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please try again later.',
                'locked' => true,
            ];
        }

        return null;
    }

    private function incrementAttempts(string $email): void
    {
        $attemptsKey = $this->getRedisKey($email, 'attempts');
        Redis::connection()->incr($attemptsKey);
        Redis::connection()->expire($attemptsKey, 3600);
    }

    private function setCooldown(string $email): void
    {
        $cooldownKey = $this->getRedisKey($email, 'cooldown');
        Redis::connection()->setex($cooldownKey, self::COOLDOWN_SECONDS, '1');
    }

    public function sendOtp(string $email, ?string $ipAddress = null): array
    {
        if ($cooldownError = $this->checkCooldown($email)) {
            return $cooldownError;
        }

        if ($attemptsError = $this->checkAttempts($email)) {
            return $attemptsError;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return [
                'success' => true,
                'message' => 'If an account exists with this email, we have sent an OTP.',
            ];
        }

        $lockKey = $this->getRedisKey($email, 'otp_lock');
        if (Redis::connection()->exists($lockKey)) {
            return [
                'success' => false,
                'message' => 'Account temporarily locked. Please try again later.',
                'locked' => true,
            ];
        }

        $resetToken = PasswordResetToken::generateFor($email, $ipAddress);

        Mail::raw("Your OTP is: {$resetToken->otp}. It expires in 10 minutes.", function ($message) use ($email) {
            $message->to($email)
                ->subject('Password Reset OTP');
        });

        $this->setCooldown($email);
        $this->incrementAttempts($email);

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'email' => $email,
            'expires_in' => 600,
            'cooldown' => self::COOLDOWN_SECONDS,
        ];
    }

    public function verifyOtp(string $email, string $otp): array
    {
        $lockKey = $this->getRedisKey($email, 'otp_lock');
        if (Redis::connection()->exists($lockKey)) {
            $remaining = Redis::connection()->ttl($lockKey);
            return [
                'success' => false,
                'message' => "Account locked. Try again in {$remaining} seconds.",
                'locked' => true,
                'lock_remaining' => $remaining,
            ];
        }

        $resetToken = PasswordResetToken::where('email', $email)
            ->where('otp', $otp)
            ->whereNull('verified_at')
            ->first();

        if (! $resetToken || ! $resetToken->isValid()) {
            $failKey = $this->getRedisKey($email, 'otp_failures');
            $failures = (int) Redis::connection()->incr($failKey);
            
            if ($failures === 1) {
                Redis::connection()->expire($failKey, 900);
            }

            if ($failures >= 5) {
                Redis::connection()->setex($lockKey, 1800, '1');
                return [
                    'success' => false,
                    'message' => 'Too many failed attempts. Account locked for 30 minutes.',
                    'locked' => true,
                ];
            }

            $remaining = 5 - $failures;
            return [
                'success' => false,
                'message' => "Invalid OTP. {$remaining} attempts remaining.",
                'attempts_remaining' => $remaining,
            ];
        }

        $failKey = $this->getRedisKey($email, 'otp_failures');
        Redis::connection()->del($failKey);

        $resetToken->markAsVerified();

        return [
            'success' => true,
            'message' => 'OTP verified successfully',
            'token' => $resetToken->id . '-' . bin2hex(random_bytes(16)),
        ];
    }

    public function resetPassword(string $email, string $token, string $password): array
    {
        [$tokenId, ] = explode('-', $token . '-', 2);

        if (! is_numeric($tokenId)) {
            return [
                'success' => false,
                'message' => 'Invalid reset token',
            ];
        }

        $resetToken = PasswordResetToken::where('id', (int) $tokenId)
            ->where('email', $email)
            ->whereNotNull('verified_at')
            ->first();

        if (! $resetToken) {
            return [
                'success' => false,
                'message' => 'Invalid reset token',
            ];
        }

        if ($resetToken->verified_at->addMinutes(30)->isPast()) {
            return [
                'success' => false,
                'message' => 'Reset token has expired',
            ];
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return [
                'success' => false,
                'message' => 'User not found',
            ];
        }

        $user->update(['password' => Hash::make($password)]);

        PasswordResetToken::where('email', $email)->delete();
        Redis::connection()->del($this->getRedisKey($email, 'attempts'));
        Redis::connection()->del($this->getRedisKey($email, 'otp_failures'));

        return [
            'success' => true,
            'message' => 'Password reset successfully',
        ];
    }
}
