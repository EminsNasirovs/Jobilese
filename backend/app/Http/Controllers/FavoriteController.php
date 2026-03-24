<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    // Add or remove favorite
    public function toggle(Request $request, $vacancyId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Please log in to favorite'], 401);
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('job_vacancy_id', $vacancyId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Removed from favorites', 'favorited' => false]);
        }

        Favorite::create([
            'user_id' => $user->id,
            'job_vacancy_id' => $vacancyId,
        ]);

        return response()->json(['message' => 'Added to favorites', 'favorited' => true]);
    }

    // Get all favorites for user
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $favorites = Favorite::with('vacancy')->where('user_id', $user->id)->get();

        return response()->json($favorites);
    }
}
