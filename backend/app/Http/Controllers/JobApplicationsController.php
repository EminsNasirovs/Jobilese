<?php

namespace App\Http\Controllers;
use App\Notifications\NewJobApplication;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationsController extends Controller
{
    /**
     * 📨 Apply for a job vacancy
     */
    public function apply(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $vacancy = JobVacancy::find($id);
        if (!$vacancy) {
            return response()->json(['message' => 'Vacancy not found'], 404);
        }

        $validated = $request->validate([
            'cover_letter' => 'nullable|string|max:2000',
            'cv'           => 'nullable|file|mimes:pdf|max:8192',
            'cv_base64'    => 'nullable|string',
            'cv_filename'  => 'nullable|string|max:255',
        ]);

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        } elseif (!empty($validated['cv_base64'])) {
            $decoded = base64_decode($validated['cv_base64'], true);
            if ($decoded !== false) {
                $filename = 'cvs/' . uniqid('cv_', true) . '.pdf';
                Storage::disk('public')->put($filename, $decoded);
                $cvPath = $filename;
            }
        }

        $application = JobApplication::create([
            'vacancy_id'   => $vacancy->id,
            'user_id'      => $user->id,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'cv_path'      => $cvPath,
        ]);
        $vacancyOwner = $vacancy->user;
        $vacancyOwner->notify(new NewJobApplication($application));
        return response()->json([
            'message'     => 'Application submitted successfully.',
            'application' => $application,
        ], 201);
    }

    /**
     * 👀 Show all job applications for employer’s own vacancies
     */
    public function employerApplications(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $applications = JobApplication::query()
            ->whereHas('vacancy', fn($q) => $q->where('user_id', $user->id))
            ->with(['vacancy:id,title', 'user:id,firstname,lastname,email'])
            ->latest()
            ->get(['id', 'vacancy_id', 'user_id', 'cover_letter', 'cv_path', 'status', 'employer_response', 'created_at']);

        // Add a preview URL for CV viewer
        $applications->transform(function ($app) {
            $app->cv_view_url = $app->cv_path ? url("/api/applications/{$app->id}/cv") : null;
            return $app;
        });

        return response()->json($applications);
    }

    /**
     * 📄 Stream CV inline (PDF viewer)
     */
    public function viewCv($id, Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $application = JobApplication::with('vacancy')->find($id);
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        if ($application->vacancy->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$application->cv_path || !Storage::disk('public')->exists($application->cv_path)) {
            return response()->json(['message' => 'CV not found'], 404);
        }

        $path = Storage::disk('public')->path($application->cv_path);

        // Use PDF viewer-friendly headers
        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
    public function getCvLink($id)
    {
        $user = request()->user();

        $application = JobApplication::with('vacancy')->findOrFail($id);

        if ($application->vacancy->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$application->cv_path || !Storage::disk('public')->exists($application->cv_path)) {
            return response()->json(['message' => 'CV not found'], 404);
        }

        $url = Storage::disk('public')->url($application->cv_path);

        return response()->json(['url' => $url]);
    }

    /**
     * 📋 Show all applications submitted by the logged-in user
     */
    public function userApplications(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $applications = JobApplication::where('user_id', $user->id)
            ->with(['vacancy:id,user_id,title,company,county,logo'])
            ->latest()
            ->get(['id', 'vacancy_id', 'cover_letter', 'cv_path', 'status', 'employer_response', 'created_at']);

        return response()->json($applications);
    }

    /**
     * ✅ Employer responds to an application (accept or deny)
     */
    public function respond(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $application = JobApplication::with('vacancy')->find($id);
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        if ($application->vacancy->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status'            => 'required|in:accepted,denied',
            'employer_response' => 'nullable|string|max:2000',
        ]);

        $application->status = $validated['status'];
        $application->employer_response = $validated['employer_response'] ?? null;
        $application->save();

        return response()->json([
            'message'     => 'Atbilde nosūtīta veiksmīgi.',
            'application' => $application,
        ]);
    }
}
