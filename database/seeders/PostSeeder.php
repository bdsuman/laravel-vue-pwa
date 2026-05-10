<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

final class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            foreach ($categories->random(2) as $category) {
                Post::factory()
                    ->published()
                    ->create([
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                    ]);
            }
        }

        // Create some drafts
        Post::factory()->count(5)->draft()->create();
    }
}
