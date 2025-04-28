<?php

namespace App\Http\Controllers\superAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class userController extends Controller
{
    public function index()
    {
        $users = User::role('user')->get();
        return response()->json($users);
    }

    public function ban($user_id)
    {
        $user = User::findOrFail($user_id);

        if (!$user->hasExactRoles(['user'])) {
            return response()->json(['message' => 'Action not allowed. Can only ban normal users.'], 403);
        }

        $user->update(['is_banned' => true]);
        
        return response()->json(['message' => 'User banned successfully']);
    }

    public function resetPassword(Request $request, $user_id)
    {
        $request->validate([
            'password' => 'required|min:8',
        ]);

        $user = User::findOrFail($user_id);
        $user->update(['password' => bcrypt($request->password)]);

        return response()->json(['message' => 'Password reset successfully']);
    }
}
