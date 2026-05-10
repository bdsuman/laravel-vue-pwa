<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_can_be_created(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Post',
        ]);

        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);
    }

    public function test_post_belongs_to_user(): void
    {
        $post = Post::factory()->create();

        $this->assertInstanceOf(User::class, $post->user);
    }

    public function test_post_belongs_to_category(): void
    {
        $post = Post::factory()->create();

        $this->assertInstanceOf(Category::class, $post->category);
    }

    public function test_post_published_scope(): void
    {
        Post::factory()->count(3)->published()->create();
        Post::factory()->count(2)->draft()->create();

        $publishedPosts = Post::published()->count();

        $this->assertEquals(3, $publishedPosts);
    }

    public function test_post_can_be_published(): void
    {
        $post = Post::factory()->draft()->create();

        $post->publish();

        $this->assertTrue($post->is_published);
        $this->assertNotNull($post->published_at);
    }
}
