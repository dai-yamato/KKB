<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $householdId = $request->header('X-Household-Id');
        
        $query = Category::where('household_id', $householdId)
            ->withCount('transactions')
            ->orderByDesc('transactions_count')
            ->orderBy('id');

        if ($request->query('type')) {
            $query->where('type', $request->query('type'));
        }
        
        return $query->get();
    }

    public function store(Request $request)
    {
        $userId = $request->header('X-User-Id');
        $user = \App\Models\User::find($userId);
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. 管理者のみ作成可能です。'], 403);
        }

        $validated = $request->validate([
            'name'  => 'required|string',
            'icon'  => 'required|string',
            'group' => 'required|string',
            'type'  => 'required|in:expense,income',
        ]);

        $validated['slug']         = strtolower($validated['name']);
        $validated['household_id'] = $request->header('X-Household-Id');

        return Category::create($validated);
    }

    public function update(Request $request, Category $category)
    {
        $userId = $request->header('X-User-Id');
        $user = \App\Models\User::find($userId);
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. 管理者のみ編集可能です。'], 403);
        }

        $householdId = $request->header('X-Household-Id');
        if ($category->household_id != $householdId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'  => 'sometimes|string',
            'icon'  => 'sometimes|string',
            'group' => 'sometimes|string',
            'type'  => 'sometimes|in:expense,income',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = strtolower($validated['name']);
        }

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy(Request $request, Category $category)
    {
        $userId = $request->header('X-User-Id');
        $user = \App\Models\User::find($userId);
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. 管理者のみ削除可能です。'], 403);
        }

        $householdId = $request->header('X-Household-Id');
        if ($category->household_id != $householdId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
