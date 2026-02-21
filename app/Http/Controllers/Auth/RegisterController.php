<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Household;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'household_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Create Household
            $household = Household::create([
                'name' => $request->household_name,
            ]);

            // 2. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'household_id' => $household->id,
                'role' => 'admin',
            ]);

            // 3. Seed Default Categories
            $this->seedDefaultCategories($household->id);

            return response()->json([
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
                'household' => [
                    'id' => $household->id,
                    'name' => $household->name,
                ]
            ]);
        });
    }

    private function seedDefaultCategories($householdId)
    {
        $categories = [
            ['name' => '食費', 'icon' => '🍎', 'slug' => 'food', 'group' => 'variable', 'type' => 'expense'],
            ['name' => '買い物', 'icon' => '🛍️', 'slug' => 'shopping', 'group' => 'variable', 'type' => 'expense'],
            ['name' => '交通費', 'icon' => '🚃', 'slug' => 'transport', 'group' => 'variable', 'type' => 'expense'],
            ['name' => '娯楽', 'icon' => '🎮', 'slug' => 'entertainment', 'group' => 'variable', 'type' => 'expense'],
            ['name' => '住居・光熱費', 'icon' => '🏠', 'slug' => 'housing', 'group' => 'fixed', 'type' => 'expense'],
            ['name' => '給与', 'icon' => '💰', 'slug' => 'salary', 'group' => 'none', 'type' => 'income'],
            ['name' => 'その他収入', 'icon' => '🎁', 'slug' => 'other_income', 'group' => 'none', 'type' => 'income'],
            ['name' => 'その他', 'icon' => '📦', 'slug' => 'other', 'group' => 'variable', 'type' => 'expense'],
        ];

        foreach ($categories as $cat) {
            $cat['household_id'] = $householdId;
            Category::create($cat);
        }
    }
}
