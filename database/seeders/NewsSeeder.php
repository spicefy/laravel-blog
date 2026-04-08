<?php
// database/seeders/NewsSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Authors ──────────────────────────────────────────────────────────
        $authors = collect([
            ['name' => 'Ada Nnamdi',   'email' => 'ada@kusoma.co',    'bio' => 'Senior tech correspondent. Covers African innovation, startups and policy.'],
            ['name' => 'Wanjiru M.',   'email' => 'wanjiru@kusoma.co','bio' => 'Education reporter based in Nairobi.'],
            ['name' => 'Chidi Okonkwo','email' => 'chidi@kusoma.co',  'bio' => 'Politics and governance writer.'],
            ['name' => 'Dele A.',      'email' => 'dele@kusoma.co',   'bio' => 'Education and scholarships desk.'],
        ])->map(fn ($u) => User::firstOrCreate(
            ['email' => $u['email']],
            ['name' => $u['name'], 'password' => bcrypt('secret'), 'bio' => $u['bio']]
        ));

        // ── Categories ───────────────────────────────────────────────────────
        $categoryData = [
            ['name' => 'Education',   'slug' => 'education',   'icon' => 'fas fa-graduation-cap', 'color' => '#0369a1', 'description' => 'KCSE, university admissions, scholarships and policy.'],
            ['name' => 'Technology',  'slug' => 'technology',  'icon' => 'fas fa-microchip',       'color' => '#2755c8', 'description' => 'African tech hubs, startups, and digital innovation.'],
            ['name' => 'Politics',    'slug' => 'politics',    'icon' => 'fas fa-landmark',        'color' => '#7c3aed', 'description' => 'Governance, elections, and policy across Africa.'],
            ['name' => 'Business',    'slug' => 'business',    'icon' => 'fas fa-chart-line',      'color' => '#0f766e', 'description' => 'Markets, startups, and African economies.'],
            ['name' => 'Culture',     'slug' => 'culture',     'icon' => 'fas fa-film',            'color' => '#d97706', 'description' => 'Music, arts, and African creative industries.'],
            ['name' => 'Sports',      'slug' => 'sports',      'icon' => 'fas fa-futbol',          'color' => '#16a34a', 'description' => 'Football, athletics, and sports news.'],
        ];
        $categories = collect($categoryData)->map(fn ($c) => Category::firstOrCreate(['slug' => $c['slug']], $c));

        // ── Tags ─────────────────────────────────────────────────────────────
        $tagNames = ['KCSE', 'University', 'Scholarships', 'AI', 'Fintech', 'Nairobi', 'Lagos', 'Kenya', 'Nigeria', 'JAMB'];
        $tags = collect($tagNames)->map(fn ($t) => Tag::firstOrCreate(['slug' => Str::slug($t)], ['name' => $t]));

        // ── Sample Education Posts ────────────────────────────────────────────
        $eduPosts = [
            [
                'title'            => 'KCSE 2025: What Changed This Year and What Students Need to Know',
                'excerpt'          => 'From revised marking schemes to new compulsory subjects, here is a complete breakdown of the 2025 KCSE changes.',
                'meta_description' => 'Full guide to the 2025 KCSE examination — revised syllabi, new compulsory subjects, grading changes, and expert preparation tips.',
                'meta_keywords'    => 'KCSE 2025, KNEC, Kenya national exams, form four, secondary school Kenya',
                'tag_ids'          => [0, 7], // KCSE, Kenya
            ],
            [
                'title'            => 'Top 20 Scholarships for Kenyan Students Studying Abroad in 2025',
                'excerpt'          => 'A curated list of fully-funded scholarships open to Kenyan and East African students for 2025 applications.',
                'meta_description' => 'Comprehensive list of 2025 fully-funded scholarships for Kenyan students: Chevening, MasterCard Foundation, DAAD, and more.',
                'meta_keywords'    => 'scholarships Kenya 2025, fully funded, Kenyan students abroad, MasterCard Foundation, Chevening',
                'tag_ids'          => [2, 7], // Scholarships, Kenya
            ],
            [
                'title'            => 'Lecturers\' Strike: A Parent\'s Diary of Six Weeks Without School',
                'excerpt'          => 'One Nairobi mother documents how the university shutdown has affected her children and what she wants government to do.',
                'meta_description' => 'Personal account of how the 2025 university lecturers\' strike affected families and students across Kenya.',
                'meta_keywords'    => 'university strike Kenya, UASU, lecturers strike 2025, public universities',
                'tag_ids'          => [1, 5], // University, Nairobi
            ],
        ];

        $eduCategory = $categories->firstWhere('slug', 'education');
        $techCategory = $categories->firstWhere('slug', 'technology');

        foreach ($eduPosts as $idx => $data) {
            $content = '<p>' . $data['excerpt'] . '</p><p>Lorem ipsum dolor sit amet consectetur. Vitae nibh mi aliquet lectus pellentesque sit ullamcorper arcu morbi. Consequat interdum fringilla urna enim.</p><h2>Key points</h2><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante.</p>';

            $post = Post::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'title'            => $data['title'],
                    'excerpt'          => $data['excerpt'],
                    'content'          => $content,
                    'category_id'      => $eduCategory->id,
                    'author_id'        => $authors->get($idx % $authors->count())->id,
                    'status'           => 'published',
                    'published_at'     => now()->subDays($idx * 2),
                    'meta_description' => $data['meta_description'],
                    'meta_keywords'    => $data['meta_keywords'],
                    'reading_time'     => Post::computeReadingTime($content),
                ]
            );

            $post->tags()->syncWithoutDetaching($tags->filter(fn ($t, $k) => in_array($k, $data['tag_ids']))->pluck('id'));
        }

        $this->command->info('✅  News seeder complete.');
    }
}
