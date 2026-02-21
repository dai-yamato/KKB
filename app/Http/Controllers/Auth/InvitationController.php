<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\HouseholdInvitation;
use App\Models\User;
use App\Models\Category;
use App\Models\Household;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Generate an invitation token for the current household.
     * Requires X-Household-Id header.
     */
    public function generate(Request $request)
    {
        $householdId = $request->header('X-Household-Id');

        if (!$householdId) {
            return response()->json(['message' => 'Household ID required'], 400);
        }

        // Invalidate old unused tokens for this household
        HouseholdInvitation::where('household_id', $householdId)
            ->whereNull('used_at')
            ->delete();

        $token = Str::random(40);

        HouseholdInvitation::create([
            'household_id' => $householdId,
            'token'        => $token,
            'expires_at'   => now()->addDays(7),
        ]);

        $url = url('/register?invite=' . $token);

        return response()->json([
            'token'      => $token,
            'url'        => $url,
            'expires_at' => now()->addDays(7)->toDateTimeString(),
        ]);
    }

    /**
     * Look up a token and return household preview (name only — no sensitive data).
     */
    public function show(string $token)
    {
        $invitation = HouseholdInvitation::where('token', $token)->first();

        if (!$invitation || !$invitation->isValid()) {
            return response()->json(['message' => '招待リンクが無効または期限切れです'], 404);
        }

        return response()->json([
            'household_name' => $invitation->household->name,
            'expires_at'     => $invitation->expires_at->toDateTimeString(),
        ]);
    }

    /**
     * Accept an invitation: register the user and join the existing household.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = HouseholdInvitation::where('token', $token)->first();

        if (!$invitation || !$invitation->isValid()) {
            return response()->json(['message' => '招待リンクが無効または期限切れです'], 422);
        }

        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'nullable|string',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'household_id' => $invitation->household_id,
            'role'         => 'editor',
        ]);

        // Mark token as used
        $invitation->update(['used_at' => now()]);

        return response()->json([
            'message'   => '参加しました',
            'user'      => [
                'id'   => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'household' => [
                'id'   => $invitation->household->id,
                'name' => $invitation->household->name,
            ],
        ]);
    }
}
