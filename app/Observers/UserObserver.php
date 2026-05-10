<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

final class UserObserver
{
    public function created(User $user): void
    {
        $this->clearUserCache($user);
        activity()
            ->performedOn($user)
            ->withProperties(['email' => $user->email])
            ->log('User registered');
    }

    public function updated(User $user): void
    {
        $this->clearUserCache($user);
        $changes = $user->getChanges();
        unset($changes['updated_at'], $changes['password']);
        if (! empty($changes)) {
            activity()
                ->performedOn($user)
                ->withProperties($changes)
                ->log('User updated');
        }
    }

    public function deleted(User $user): void
    {
        $this->clearUserCache($user);
        activity()
            ->performedOn($user)
            ->withProperties(['email' => $user->email])
            ->log('User deleted');
    }

    public function restored(User $user): void
    {
        $this->clearUserCache($user);
    }

    public function forceDeleted(User $user): void
    {
        $this->clearUserCache($user);
    }

    private function clearUserCache(User $user): void
    {
        Cache::forget("user.{$user->id}");
    }
}
