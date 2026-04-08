<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run()
    {
        // Create categories
        $categories = [
            ['name' => 'Education News', 'slug' => 'education-news', 'css_suffix' => 'blue-600', 'icon' => 'fas fa-graduation-cap', 'is_active' => true],
            ['name' => 'Technology', 'slug' => 'technology', 'css_suffix' => 'green-600', 'icon' => 'fas fa-microchip', 'is_active' => true],
            ['name' => 'KCSE Updates', 'slug' => 'kcse-updates', 'css_suffix' => 'orange-600', 'icon' => 'fas fa-file-alt', 'is_active' => true],
            ['name' => 'Scholarships', 'slug' => 'scholarships', 'css_suffix' => 'purple-600', 'icon' => 'fas fa-dollar-sign', 'is_active' => true],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Get or create author
        $author = User::first();
        if (!$author) {
            $author = User::create([
                'name' => 'Admin User',
                'email' => 'admin@kusoma.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create sample posts
        Category::all()->each(function ($category) use ($author) {
            for ($i = 1; $i <= 3; $i++) {
                Post::create([
                    'title' => "Sample {$category->name} Article {$i}",
                    'slug' => Str::slug("sample {$category->name} article {$i}"),
                    'excerpt' => "This is a sample excerpt for {$category->name} article {$i}. It provides a brief overview of what this article covers.",
                    'content' => "<h2>Introduction</h2><p>This is the full content of the sample article. It contains detailed information about {$category->name}.</p><h3>Key Points</h3><ul><li>Important point one</li><li>Important point two</li><li>Important point three</li></ul>",
                    'category_id' => $category->id,
                    'user_id' => $author->id,
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(1, 30)),
                    'reading_time' => rand(3, 10),
                    'view_count' => rand(0, 1000),
                ]);
            }
        });
    }
}