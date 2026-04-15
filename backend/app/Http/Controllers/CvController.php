<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CvDetail; // Ensure you created this model in Step 1
use Illuminate\Support\Facades\Auth;

class CvController extends Controller
{
    // Fetch the user's CV
    public function show()
    {
        $cv = CvDetail::where('user_id', Auth::id())->first();
        return response()->json($cv);
    }

    // Save or Update the CV
    public function store(Request $request)
    {
        $cv = CvDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'summary'    => $request->summary,
                'experience' => $request->experience, // Laravel casts this to JSON
                'education'  => $request->education,
                'skills'     => $request->skills,
            ]
        );

        return response()->json(['message' => 'CV saglabāts!', 'cv' => $cv]);
    }
}