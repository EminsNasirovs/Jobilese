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

        // Validē ievadi
        $validated = $request->validate([
            'comment_text' => 'required|string|max:1000',
            'vacancy_id'   => 'required|exists:job_vacancies,id',
        ]);

        // Izveido komentāru
        $comment = new Comments();
        $comment->vacancy_id = $validated['vacancy_id'];
        $comment->comment_text = $validated['comment_text'];
        $comment->user_id = $user->id;
        $comment->save();

        // Ielādē saistīto lietotāju (lai frontend uzreiz redzētu vārdu)
        $comment->load('user:id,username');

        return response()->json([
            'message' => 'Komentārs pievienots veiksmīgi!',
            'data' => $comment,
        ], 201);
    }

    /**
     * Atgriež visus komentārus konkrētajai vakancei
     */
    public function index($vacancyId)
    {
        $comments = Comments::where('vacancy_id', $vacancyId)
            ->with('user:id,username')
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
