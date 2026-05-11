<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_can_be_created(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('posts', [
            'title' => $post->title,
            'user_id' => $user->id,
        ]);
    }

    public function test_post_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(User::class, $post->author);
        $this->assertEquals($user->id, $post->author->id);
    }

    public function test_post_belongs_to_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertEquals($category->id, $post->category->id);
    }

    public function test_post_published_scope(): void
    {
        $user = User::factory()->create();
        
        Post::factory()->published()->create(['user_id' => $user->id]);
        Post::factory()->draft()->create(['user_id' => $user->id]);

        $this->assertEquals(1, Post::published()->count());
    }

    public function test_post_can_be_published(): void
    {
        $user = User::factory()->create();
        
        $post = Post::factory()->draft()->create(['user_id' => $user->id]);
        $this->assertFalse($post->is_published);

        $post->is_published = true;
        $post->save();

        $this->assertTrue($post->fresh()->is_published);
    }
}
