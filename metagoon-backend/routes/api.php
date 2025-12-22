<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobVacanciesController;
use App\Http\Controllers\JobApplicationsController;
use App\Http\Controllers\JobComments;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/stats', [\App\Http\Controllers\StatsController::class, 'index']);

// Public vacancies
Route::get('/vacancies', [JobVacanciesController::class, 'index']);
Route::get('/vacancies/{id}', [JobVacanciesController::class, 'show']);

// Protected routes (require login)
Route::middleware('auth:sanctum')->group(function () {
    // Notifications
    Route::get('/notifications', function (Request $request) {
        return $request->user()->notifications;
    });

    // User profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::delete('/profile/delete', [UserController::class, 'destroy']);

    // Auth user helper
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    // Favorites
    Route::post('/favorites/{vacancyId}', [FavoriteController::class, 'toggle']);
    Route::get('/favorites', [FavoriteController::class, 'index']);

    // Vacancy CRUD & applications
    Route::post('/vacancies', [JobVacanciesController::class, 'store']);
    Route::put('/vacancies/{id}', [JobVacanciesController::class, 'update']);
    Route::delete('/vacancies/{id}', [JobVacanciesController::class, 'destroy']);
    Route::post('/vacancies/{id}/apply', [JobApplicationsController::class, 'apply']);
    Route::get('/applications', [JobApplicationsController::class, 'employerApplications']);
    Route::get('/applications/{id}/cv/view', [JobApplicationsController::class, 'viewCv']);
    Route::get('/applications/{id}/cv-link', [JobApplicationsController::class, 'getCvLink']);

    // Comments
    Route::post('/comments', [JobComments::class, 'store']);
    Route::get('/vacancies/{id}/comments', [JobComments::class, 'index']);
    Route::delete('/comments/{id}', [JobComments::class, 'destroy']);

    // File uploads
    Route::post('/upload', [JobVacanciesController::class, 'upload']);

    // Admin
    Route::middleware('admin')->get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
