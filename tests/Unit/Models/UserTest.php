<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
    }

    public function test_user_has_many_posts(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertCount(1, $user->posts);
    }

    public function test_user_can_be_assigned_role(): void
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $user = User::factory()->create();
        
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_user_active_scope(): void
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);

        $this->assertEquals(1, User::active()->count());
    }

    public function test_user_search_scope(): void
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $results = User::search('John')->get();
        $this->assertEquals(1, $results->count());
    }
}
