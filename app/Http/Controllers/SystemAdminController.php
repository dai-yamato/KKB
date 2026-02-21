<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemAdmin;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class SystemAdminController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = SystemAdmin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'メールアドレスまたはパスワードが正しくありません'], 401);
        }

        return response()->json([
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
            ]
        ]);
    }

    private function isAuthenticated(Request $request)
    {
        $adminId = $request->header('X-SystemAdmin-Id');
        if (!$adminId) return false;
        
        $admin = SystemAdmin::find($adminId);
        return $admin !== null;
    }

    public function getHouseholds(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['message' => 'Unauthorized. System Admin only.'], 403);
        }

        $households = \App\Models\Household::with('users:id,household_id,name,email,role,created_at')
            ->withCount(['transactions', 'categories', 'budgets'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($households);
    }

    public function getLogs(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['message' => 'Unauthorized. System Admin only.'], 403);
        }

        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return response()->json(['logs' => 'No logs found.']);
        }

        $file = File::get($logPath);
        
        if (strlen($file) > 100000) {
            $file = substr($file, -100000);
            $file = "...(truncated)...\n" . $file;
        }

        return response()->json(['logs' => $file]);
    }

    public function getDefaultCategories(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['message' => 'Unauthorized. System Admin only.'], 403);
        }

        $setting = SystemSetting::where('key', 'default_categories')->first();
        return response()->json($setting ? $setting->value : []);
    }

    public function updateDefaultCategories(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['message' => 'Unauthorized. System Admin only.'], 403);
        }

        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.name' => 'required|string',
            'categories.*.icon' => 'required|string',
            'categories.*.slug' => 'required|string',
            'categories.*.group' => 'required|string',
            'categories.*.type' => 'required|in:expense,income',
        ]);

        $setting = SystemSetting::firstOrCreate(['key' => 'default_categories']);
        $setting->value = $validated['categories'];
        $setting->save();

        return response()->json(['message' => 'System default categories updated successfully.']);
    }
}
