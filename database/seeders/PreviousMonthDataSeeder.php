<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Household;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;

class PreviousMonthDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first household
        $household = Household::first();
        if (!$household) {
            $this->command->error('No household found to seed data.');
            return;
        }

        $user = $household->users()->first();
        if (!$user) {
            $this->command->error('No user found in the household.');
            return;
        }

        // Get categories
        $categories = Category::where('household_id', $household->id)->get();
        if ($categories->isEmpty()) {
            $this->command->error('No categories found for the household.');
            return;
        }

        $expenseCategories = $categories->where('type', 'expense');
        $incomeCategories = $categories->where('type', 'income');

        if ($expenseCategories->isEmpty() || $incomeCategories->isEmpty()) {
            $this->command->warn('Missing either expense or income categories, doing best effort.');
        }

        // Generate data for the previous month
        $lastMonth = Carbon::now()->subMonth();
        $daysInMonth = $lastMonth->daysInMonth;

        $transactionsToInsert = [];

        // 1. Income (Salary)
        if ($incomeCategories->isNotEmpty()) {
            $salaryCat = $incomeCategories->first();
            $transactionsToInsert[] = [
                'household_id' => $household->id,
                'user_id' => $user->id,
                'category_id' => $salaryCat->id,
                'type' => 'income',
                'amount' => 350000,
                'date' => $lastMonth->copy()->startOfMonth()->addDays(24)->toDateString(),
                'note' => '先月分 給与',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 2. Fixed Expenses (Housing, Utilities)
        if ($expenseCategories->isNotEmpty()) {
            $fixedCat = $expenseCategories->where('group', 'fixed')->first() ?? $expenseCategories->first();
            $transactionsToInsert[] = [
                'household_id' => $household->id,
                'user_id' => $user->id,
                'category_id' => $fixedCat->id,
                'type' => 'expense',
                'amount' => 85000,
                'date' => $lastMonth->copy()->startOfMonth()->addDays(26)->toDateString(),
                'note' => '家賃・光熱費',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 3. Variable Expenses (Food, etc.)
            $variableCats = $expenseCategories->where('group', '!=', 'fixed');
            if ($variableCats->isNotEmpty()) {
                // Generate ~15 random expenses throughout the month
                for ($i = 0; $i < 15; $i++) {
                    $randomCat = $variableCats->random();
                    $randomDay = rand(1, $daysInMonth);
                    
                    $amount = 0;
                    $note = '';

                    // Some pseudo-random logic for realism
                    if (str_contains($randomCat->name, '食')) {
                        $amount = rand(800, 5000);
                        $note = rand(0, 1) ? 'スーパー' : 'ランチ';
                    } elseif (str_contains($randomCat->name, '交通')) {
                        $amount = rand(500, 2000);
                        $note = '電車代';
                    } else {
                        $amount = rand(1000, 10000);
                        $note = '買い物';
                    }

                    $transactionsToInsert[] = [
                        'household_id' => $household->id,
                        'user_id' => $user->id,
                        'category_id' => $randomCat->id,
                        'type' => 'expense',
                        'amount' => $amount,
                        'date' => $lastMonth->copy()->startOfMonth()->addDays($randomDay - 1)->toDateString(),
                        'note' => $note,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        Transaction::insert($transactionsToInsert);

        $this->command->info('Seeded ' . count($transactionsToInsert) . ' transactions for the previous month.');
    }
}
