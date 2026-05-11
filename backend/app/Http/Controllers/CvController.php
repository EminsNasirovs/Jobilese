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
        $validated = $request->validate([
            'summary'    => 'nullable|string|max:2000',
            'experience' => 'nullable|array',
            'education'  => 'nullable|array',
            'skills'     => 'nullable|array',
            'template'   => 'nullable|in:editorial,sidebar,minimal',
        ]);

        $cv = CvDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'summary'    => $validated['summary']    ?? null,
                'experience' => $validated['experience'] ?? [],
                'education'  => $validated['education']  ?? [],
                'skills'     => $validated['skills']     ?? [],
                'template'   => $validated['template']   ?? 'editorial',
            ]
        );

        return response()->json(['message' => 'CV saglabāts!', 'cv' => $cv]);
    }
}