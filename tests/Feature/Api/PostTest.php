<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_posts(): void
    {
        Sanctum::actingAs($this->user);
        
        Post::factory()->count(3)->published()->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
            ]);
    }

    public function test_can_create_post(): void
    {
        Sanctum::actingAs($this->user);
        
        $category = Category::factory()->create();

        $response = $this->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'Post content here',
            'category_id' => $category->id,
            'is_published' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Test Post');

        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);
    }

    public function test_can_update_post(): void
    {
        Sanctum::actingAs($this->user);
        
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_can_delete_post(): void
    {
        Sanctum::actingAs($this->user);
        
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_can_publish_post(): void
    {
        Sanctum::actingAs($this->user);
        
        $post = Post::factory()->draft()->create(['user_id' => $this->user->id]);
        
        $response = $this->postJson("/api/posts/{$post->id}/publish");

        $response->assertStatus(200)
            ->assertJsonPath('data.is_published', true);
    }
}
