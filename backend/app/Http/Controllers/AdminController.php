<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'users' => [
                'total'        => User::count(),
                'candidates'   => User::where('role', 'bezdarbnieks')->count(),
                'employers'    => User::where('role', 'uzņēmējs')->count(),
                'workers'      => User::where('role', 'darbinieks')->count(),
                'admins'       => User::where('role', 'admin')->count(),
            ],
            'vacancies'    => JobVacancy::count(),
            'applications' => JobApplication::count(),
        ]);
    }

    public function users()
    {
        $users = User::select('id', 'firstname', 'lastname', 'username', 'email', 'role', 'company_name', 'created_at')
            ->latest()
            ->get();

        return response()->json($users);
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Nevar dzēst pašu sevi.'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Lietotājs dzēsts.']);
    }

    public function vacancies()
    {
        $vacancies = JobVacancy::with('user:id,firstname,lastname,company_name')
            ->select('id', 'user_id', 'title', 'company', 'category', 'county', 'salary', 'salary_type', 'created_at')
            ->latest()
            ->get();

        return response()->json($vacancies);
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

    public function applications()
    {
        $applications = JobApplication::with([
            'user:id,firstname,lastname,email',
            'vacancy:id,title,company',
        ])
            ->select('id', 'user_id', 'vacancy_id', 'cover_letter', 'cv_path', 'created_at')
            ->latest()
            ->get();

        return response()->json($applications);
    }
}
