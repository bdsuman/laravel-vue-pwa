<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'icon' => 'cpu', 'color' => '#3B82F6', 'order' => 1],
            ['name' => 'Business', 'slug' => 'business', 'icon' => 'briefcase', 'color' => '#10B981', 'order' => 2],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'icon' => 'heart', 'color' => '#F59E0B', 'order' => 3],
            ['name' => 'Health', 'slug' => 'health', 'icon' => 'plus-circle', 'color' => '#EF4444', 'order' => 4],
            ['name' => 'Education', 'slug' => 'education', 'icon' => 'book-open', 'color' => '#8B5CF6', 'order' => 5],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
