<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Conversation;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $weekAgo = now()->subDays(7);

        return response()->json([
            'users' => [
                'total'      => User::count(),
                'candidates' => User::where('role', 'bezdarbnieks')->count(),
                'employers'  => User::where('role', 'uzņēmējs')->count(),
                'workers'    => User::where('role', 'darbinieks')->count(),
                'admins'     => User::where('role', 'admin')->count(),
                'new_week'   => User::where('created_at', '>=', $weekAgo)->count(),
            ],
            'vacancies' => [
                'total'    => JobVacancy::count(),
                'new_week' => JobVacancy::where('created_at', '>=', $weekAgo)->count(),
            ],
            'applications' => [
                'total'    => JobApplication::count(),
                'pending'  => JobApplication::where('status', 'pending')->count(),
                'accepted' => JobApplication::where('status', 'accepted')->count(),
                'denied'   => JobApplication::where('status', 'denied')->count(),
                'new_week' => JobApplication::where('created_at', '>=', $weekAgo)->count(),
            ],
            'chats' => [
                'conversations' => Conversation::count(),
                'messages'      => Message::count(),
            ],
            'comments' => Comments::count(),
        ]);
    }

    public function users(Request $request)
    {
        $q = User::select('id', 'firstname', 'lastname', 'username', 'email', 'role', 'company_name', 'created_at');

        if ($search = $request->query('search')) {
            $q->where(function ($w) use ($search) {
                $w->where('firstname', 'like', "%$search%")
                  ->orWhere('lastname', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($role = $request->query('role')) {
            $q->where('role', $role);
        }

        return response()->json($q->latest()->get());
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Nevar dzēst pašu sevi.'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Lietotājs dzēsts.']);
    }

    public function updateUserRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:bezdarbnieks,darbinieks,uzņēmējs,admin',
        ]);

        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            return response()->json(['message' => 'Nevar atņemt sev admin lomu.'], 400);
        }

        $user->role = $validated['role'];
        $user->save();

        return response()->json(['message' => 'Loma atjaunota.', 'user' => $user]);
    }

    public function vacancies(Request $request)
    {
        $q = JobVacancy::with('user:id,firstname,lastname,company_name')
            ->select('id', 'user_id', 'title', 'company', 'category', 'county', 'salary', 'salary_type', 'created_at');

        if ($search = $request->query('search')) {
            $q->where(function ($w) use ($search) {
                $w->where('title', 'like', "%$search%")
                  ->orWhere('company', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
            });
        }

        return response()->json($q->latest()->get());
    }

    public function deleteVacancy($id)
    {
        $vacancy = JobVacancy::findOrFail($id);

        if ($vacancy->logo && \Storage::disk('public')->exists($vacancy->logo)) {
            \Storage::disk('public')->delete($vacancy->logo);
        }

        $vacancy->delete();

        return response()->json(['message' => 'Vakance dzēsta.']);
    }

    public function applications(Request $request)
    {
        $q = JobApplication::with([
            'user:id,firstname,lastname,email',
            'vacancy:id,title,company',
        ])->select('id', 'user_id', 'vacancy_id', 'cover_letter', 'cv_path', 'status', 'employer_response', 'created_at');

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        return response()->json($q->latest()->get());
    }

    public function deleteApplication($id)
    {
        $application = JobApplication::findOrFail($id);

        if ($application->cv_path && \Storage::disk('public')->exists($application->cv_path)) {
            \Storage::disk('public')->delete($application->cv_path);
        }

        $application->delete();

        return response()->json(['message' => 'Pieteikums dzēsts.']);
    }

    public function comments(Request $request)
    {
        $q = Comments::with([
            'user:id,username,firstname,lastname',
        ])->select('id', 'user_id', 'vacancy_id', 'comment_text', 'parent_id', 'created_at');

        if ($search = $request->query('search')) {
            $q->where('comment_text', 'like', "%$search%");
        }

        return response()->json($q->latest()->get());
    }

    public function deleteComment($id)
    {
        $comment = Comments::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Komentārs dzēsts.']);
    }
}
