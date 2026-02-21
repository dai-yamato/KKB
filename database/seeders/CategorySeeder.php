<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => '食費', 'icon' => '🍎', 'slug' => 'food', 'group' => 'variable'],
            ['name' => '買い物', 'icon' => '🛍️', 'slug' => 'shopping', 'group' => 'variable'],
            ['name' => '交通費', 'icon' => '🚃', 'slug' => 'transport', 'group' => 'variable'],
            ['name' => '娯楽', 'icon' => '🎮', 'slug' => 'entertainment', 'group' => 'variable'],
            ['name' => '住居・光熱費', 'icon' => '🏠', 'slug' => 'housing', 'group' => 'fixed'],
            ['name' => '給与', 'icon' => '💰', 'slug' => 'salary', 'group' => 'none'],
            ['name' => 'その他', 'icon' => '📦', 'slug' => 'other', 'group' => 'variable']
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::create($cat);
        }
    }
}
