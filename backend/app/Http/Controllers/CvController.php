<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CvDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CvController extends Controller
{
    /**
     * List all CVs belonging to the current user.
     */
    public function index()
    {
        $cvs = CvDetail::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->latest('updated_at')
            ->get();

        return response()->json($cvs);
    }

    /**
     * Fetch a single CV (must belong to the current user).
     */
    public function show($id)
    {
        $cv = CvDetail::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($cv);
    }

    /**
     * Create a new CV for the current user.
     */
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        // If the new CV is marked default, clear default on the rest.
        if (!empty($data['is_default'])) {
            CvDetail::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $cv = CvDetail::create(array_merge($data, ['user_id' => Auth::id()]));

        return response()->json(['message' => 'CV izveidots!', 'cv' => $cv], 201);
    }

    /**
     * Update an existing CV.
     */
    public function update(Request $request, $id)
    {
        $cv = CvDetail::where('user_id', Auth::id())->findOrFail($id);

        $data = $this->validatePayload($request);

        if (!empty($data['is_default'])) {
            CvDetail::where('user_id', Auth::id())
                ->where('id', '!=', $cv->id)
                ->update(['is_default' => false]);
        }

        $cv->update($data);

        return response()->json(['message' => 'CV saglabāts!', 'cv' => $cv]);
    }

    /**
     * Delete a CV (ownership enforced).
     */
    public function destroy($id)
    {
        $cv = CvDetail::where('user_id', Auth::id())->findOrFail($id);
        $cv->delete();

        return response()->json(['message' => 'CV dzēsts.']);
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'title'      => 'nullable|string|max:120',
            'is_default' => 'nullable|boolean',
            'summary'    => 'nullable|string|max:2000',
            'experience' => 'nullable|array',
            'education'  => 'nullable|array',
            'skills'     => 'nullable|array',
            'template'   => 'nullable|in:editorial,sidebar,minimal',
        ]);
    }
}
