<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Budget;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $householdId = $request->header('X-Household-Id');
        if ($request->has('all')) {
            return Budget::where('household_id', $householdId)->get();
        }
        $month = $request->query('month', now()->format('Y-m'));
        return Budget::where('household_id', $householdId)
            ->where('month', $month)
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|integer',
            'month' => 'required|string',
        ]);

        $validated['household_id'] = $request->header('X-Household-Id');

        return Budget::updateOrCreate(
            [
                'household_id' => $validated['household_id'],
                'category_id' => $validated['category_id'],
                'month' => $validated['month']
            ],
            ['amount' => $validated['amount']]
        );
    }
}
