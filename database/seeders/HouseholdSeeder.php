<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Household;
use App\Models\User;
use App\Models\Category;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;

class HouseholdSeeder extends Seeder
{
    public function run(): void
    {
        // Yamada Family
        $yamada = Household::create(['name' => '山田家']);
        User::create([
            'name' => '山田太郎',
            'email' => 'yamada.taro@example.com',
            'password' => Hash::make('password'),
            'household_id' => $yamada->id,
            'role' => 'admin',
        ]);
        User::create([
            'name' => '山田花子',
            'email' => 'yamada.hanako@example.com',
            'password' => Hash::make('password'),
            'household_id' => $yamada->id,
            'role' => 'editor',
        ]);
        User::create([
            'name' => '共通',
            'email' => 'yamada.common@example.com',
            'password' => Hash::make('password'),
            'household_id' => $yamada->id,
            'role' => 'editor',
        ]);

        // Default categories for Yamada
        $this->seedCategories($yamada->id);

        // Tanaka Family
        $tanaka = Household::create(['name' => '田中家']);
        User::create([
            'name' => '田中次郎',
            'email' => 'tanaka.jiro@example.com',
            'password' => Hash::make('password'),
            'household_id' => $tanaka->id,
            'role' => 'admin',
        ]);
        User::create([
            'name' => '田中幸子',
            'email' => 'tanaka.sachiko@example.com',
            'password' => Hash::make('password'),
            'household_id' => $tanaka->id,
            'role' => 'editor',
        ]);

        $this->seedCategories($tanaka->id);
    }

    private function seedCategories($householdId)
    {
        $sysCatSetting = SystemSetting::firstOrCreate([
            'key' => 'default_categories'
        ], [
            'value' => [
                ['name' => '食費', 'icon' => '🍎', 'slug' => 'food', 'group' => 'variable', 'type' => 'expense'],
                ['name' => '買い物', 'icon' => '🛍️', 'slug' => 'shopping', 'group' => 'variable', 'type' => 'expense'],
                ['name' => '交通費', 'icon' => '🚃', 'slug' => 'transport', 'group' => 'variable', 'type' => 'expense'],
                ['name' => '娯楽', 'icon' => '🎮', 'slug' => 'entertainment', 'group' => 'variable', 'type' => 'expense'],
                ['name' => '住居・光熱費', 'icon' => '🏠', 'slug' => 'housing', 'group' => 'fixed', 'type' => 'expense'],
                ['name' => '給与', 'icon' => '💰', 'slug' => 'salary', 'group' => 'none', 'type' => 'income'],
                ['name' => 'その他収入', 'icon' => '🎁', 'slug' => 'other_income', 'group' => 'none', 'type' => 'income'],
                ['name' => 'その他', 'icon' => '📦', 'slug' => 'other', 'group' => 'variable', 'type' => 'expense'],
            ]
        ]);

        $categories = $sysCatSetting->value;

        foreach ($categories as $cat) {
            $cat['household_id'] = $householdId;
            Category::create($cat);
        }
    }
}
