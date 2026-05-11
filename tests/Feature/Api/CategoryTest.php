<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_categories(): void
    {
        Sanctum::actingAs($this->user);
        
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
            ]);
    }

    public function test_can_create_category(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Test Category');

        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    public function test_can_update_category(): void
    {
        Sanctum::actingAs($this->user);
        
        $category = Category::factory()->create();

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Category');
    }

    public function test_can_delete_category(): void
    {
        Sanctum::actingAs($this->user);
        
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_can_toggle_category_status(): void
    {
        Sanctum::actingAs($this->user);
        
        $category = Category::factory()->create(['is_active' => true]);

        $response = $this->postJson("/api/categories/{$category->id}/toggle");

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_active' => false]);
    }
}
