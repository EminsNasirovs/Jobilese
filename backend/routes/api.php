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
use App\Http\Controllers\CvController;
use App\Http\Controllers\ChatController;

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
    Route::get('/notifications/unread-count', function (Request $request) {
        return response()->json(['count' => $request->user()->unreadNotifications()->count()]);
    });
    Route::post('/notifications/read', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'ok']);
    });

    // User profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::delete('/profile/delete', [UserController::class, 'destroy']);
    // CV routes (multi-CV per user)
    Route::get('/cv', [CvController::class, 'index']);
    Route::post('/cv', [CvController::class, 'store']);
    Route::get('/cv/{id}', [CvController::class, 'show']);
    Route::put('/cv/{id}', [CvController::class, 'update']);
    Route::delete('/cv/{id}', [CvController::class, 'destroy']);
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
    Route::get('/my-applications', [JobApplicationsController::class, 'userApplications']);
    Route::post('/applications/{id}/respond', [JobApplicationsController::class, 'respond']);
    Route::get('/applications/{id}/cv/view', [JobApplicationsController::class, 'viewCv']);
    Route::get('/applications/{id}/cv-link', [JobApplicationsController::class, 'getCvLink']);

    // Comments
    Route::post('/comments', [JobComments::class, 'store']);
    Route::get('/vacancies/{id}/comments', [JobComments::class, 'index']);
    Route::delete('/comments/{id}', [JobComments::class, 'destroy']);

    // Chat
    Route::get('/chat/conversations', [ChatController::class, 'conversations']);
    Route::post('/chat/start', [ChatController::class, 'startOrGet']);
    Route::get('/chat/{id}/messages', [ChatController::class, 'messages']);
    Route::post('/chat/{id}/send', [ChatController::class, 'send']);
    Route::delete('/chat/{id}', [ChatController::class, 'destroy']);
    Route::get('/chat/unread', [ChatController::class, 'unreadCount']);

    // File uploads
    Route::post('/upload', [JobVacanciesController::class, 'upload']);

    // Admin
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::patch('/users/{id}/role', [AdminController::class, 'updateUserRole']);
        Route::get('/vacancies', [AdminController::class, 'vacancies']);
        Route::delete('/vacancies/{id}', [AdminController::class, 'deleteVacancy']);
        Route::get('/applications', [AdminController::class, 'applications']);
        Route::delete('/applications/{id}', [AdminController::class, 'deleteApplication']);
        Route::get('/comments', [AdminController::class, 'comments']);
        Route::delete('/comments/{id}', [AdminController::class, 'deleteComment']);
    });
});
