<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\User;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // If user is logged in, attach their ID and override email to prevent spoofing
        $userId = $request->header('X-User-Id');
        if ($userId && $userId !== 'null' && $userId !== 'undefined') {
            $user = User::find($userId);
            if ($user) {
                $validated['user_id'] = $user->id;
                $validated['email'] = $user->email;
                if (empty($validated['name'])) {
                    $validated['name'] = $user->name;
                }
            }
        }

        Contact::create($validated);

        return response()->json(['message' => 'お問い合わせを送信しました。']);
    }

    public function index(Request $request)
    {
        // Require System Admin
        $adminId = $request->header('X-SystemAdmin-Id');
        if (!$adminId || !\App\Models\SystemAdmin::find($adminId)) {
            return response()->json(['message' => 'Unauthorized. System Admin only.'], 403);
        }

        $contacts = Contact::with('user:id,name,email')->orderBy('created_at', 'desc')->get();
        return response()->json($contacts);
    }
}
