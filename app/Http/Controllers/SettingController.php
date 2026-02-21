<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Household;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'メールアドレスまたはパスワードが正しくありません'], 401);
        }

        return response()->json([
            'user' => [
                'id'   => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'household' => [
                'id'   => $user->household->id,
                'name' => $user->household->name,
            ],
        ]);
    }

    public function households()
    {
        return Household::all();
    }

    public function updateHousehold(Request $request)
    {
        $householdId = $request->header('X-Household-Id');
        $household = Household::findOrFail($householdId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $household->update($validated);
        return $household;
    }

    public function users(Household $household)
    {
        return $household->users;
    }

    public function addUser(Request $request)
    {
        $householdId = $request->header('X-Household-Id');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:6'
        ]);

        $validated['household_id'] = $householdId;
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            $validated['password'] = bcrypt('password'); // Default password
        }

        return User::create($validated);
    }

    public function updateRole(Request $request, User $user)
    {
        $householdId = $request->header('X-Household-Id');
        if ($user->household_id != $householdId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,editor',
        ]);

        if ($user->role === 'admin' && $validated['role'] === 'editor') {
            $adminCount = User::where('household_id', $householdId)->where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json(['message' => '管理者は最低1人必要です。'], 400);
            }
        }

        $user->update(['role' => $validated['role']]);
        return response()->json(['message' => 'Role updated']);
    }

    public function deleteUser(Request $request, User $user)
    {
        $householdId = $request->header('X-Household-Id');
        if ($user->household_id != $householdId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        if ($user->role === 'admin') {
            $adminCount = User::where('household_id', $householdId)->where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json(['message' => '最後の管理者は削除できません。'], 400);
            }
        }

        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function reset(Request $request)
    {
        $userId = $request->header('X-User-Id');
        $user = User::find($userId);
        
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => '全てのデータをリセットする機能は管理者のみ利用できます。'], 403);
        }

        $householdId = $request->header('X-Household-Id');
        Transaction::where('household_id', $householdId)->delete();
        Budget::where('household_id', $householdId)->delete();
        return response()->json(['message' => 'System reset']);
    }
}
