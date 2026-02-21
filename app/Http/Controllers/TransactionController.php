<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $householdId = $request->header('X-Household-Id');
        return Transaction::where('household_id', $householdId)
            ->with(['category', 'user'])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|integer',
            'note' => 'nullable|string',
        ]);

        $validated['household_id'] = $request->header('X-Household-Id');

        return Transaction::create($validated);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $userId = $request->header('X-User-Id');
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check time rule
        $hoursPassed = $transaction->created_at->diffInHours(now());
        if ($user->role !== 'admin' && $hoursPassed > 24) {
            return response()->json(['message' => '登録から24時間経過した記録は管理者しか削除できません。'], 403);
        }

        $householdId = $request->header('X-Household-Id');
        if ($transaction->household_id != $householdId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }
}
