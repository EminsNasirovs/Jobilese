<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comments;
use App\Models\User;

class JobComments extends Controller
{
    /**
     * Saglabā jaunu komentāru
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'comment_text' => 'required|string|max:1000',
            'vacancy_id'   => 'required|exists:job_vacancies,id',
            'parent_id'    => 'nullable|exists:job_comment,id',
        ]);

        $comment = new Comments();
        $comment->vacancy_id = $validated['vacancy_id'];
        $comment->comment_text = $validated['comment_text'];
        $comment->user_id = $user->id;
        $comment->parent_id = $validated['parent_id'] ?? null;
        $comment->save();

        $comment->load('user:id,username', 'replies');

        return response()->json([
            'message' => 'Komentārs pievienots veiksmīgi!',
            'data' => $comment,
        ], 201);
    }

    /**
     * Atgriež visus komentārus konkrētajai vakancei (tikai top-level, ar replies)
     */
    public function index($vacancyId)
    {
        $comments = Comments::where('vacancy_id', $vacancyId)
            ->whereNull('parent_id')
            ->with('user:id,username', 'replies')
            ->latest()
            ->get();

        return response()->json([
            'data' => $comments,
        ]);
    }

    /**
     * Dzēš komentāru
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $comment = Comments::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Komentārs nav atrasts'], 404);
        }

        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Nav atļauts dzēst šo komentāru'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Komentārs veiksmīgi dzēsts']);
    }
}
