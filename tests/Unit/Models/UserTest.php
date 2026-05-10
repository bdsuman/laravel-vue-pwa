<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
        $this->assertEquals('Test User', $user->name);
    }

    public function test_user_has_many_posts(): void
    {
        $user = User::factory()->create();
        
        $this->assertArrayHasKey('posts', $user->getRelations());
    }

    public function test_user_can_be_assigned_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_user_active_scope(): void
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);

        $activeUsers = User::active()->count();

        $this->assertEquals(3, $activeUsers);
    }

    public function test_user_search_scope(): void
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $results = User::search('john')->get();

        $this->assertEquals(1, $results->count());
        $this->assertEquals('John Doe', $results->first()->name);
    }
}
