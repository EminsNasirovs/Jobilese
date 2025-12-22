<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    // Fetch user profile
    public function profile()
    {
        return response()->json(Auth::user());
    }

    // Update user profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'role' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'company_number' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Update user info
        $user->update($validated);

        // Return updated profile
        return response()->json($user);
    }
    // Delete user profile
    public function destroy(Request $request){
            $user = $request->user();
            $password = $request->input('password');

            if (!$password || !Hash::check($password, $user->password)) {
                return response()->json(['message' => 'Nepareiza parole.'], 400);
            }

            $user->delete();

            return response()->json(['message' => 'Konts veiksmīgi dzēsts.']);
        }
}
