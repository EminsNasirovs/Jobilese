<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobVacancy;

class StatsController extends Controller
{
    public function index()
    {
        $vacancies = JobVacancy::count();
        $companies = User::where('role', 'uzņēmējs')->count();
        $unemployed = User::where('role', 'bezdarbnieks')->count();

        return response()->json([
            'vacancies' => $vacancies,
            'companies' => $companies,
            'unemployed' => $unemployed,
        ]);
    }
}
