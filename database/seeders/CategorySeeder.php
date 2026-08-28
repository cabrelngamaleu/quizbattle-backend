<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Culture générale', 'slug' => 'culture-generale', 'icon' => '🧠'],
            ['name' => 'Sport', 'slug' => 'sport', 'icon' => '⚽'],
            ['name' => 'Cinéma & Séries', 'slug' => 'cinema-series', 'icon' => '🎬'],
            ['name' => 'Afrique & Cameroun', 'slug' => 'afrique-cameroun', 'icon' => '🌍'],
            ['name' => 'Sciences', 'slug' => 'sciences', 'icon' => '🔬'],
            ['name' => 'Musique', 'slug' => 'musique', 'icon' => '🎵'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
