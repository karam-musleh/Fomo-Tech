<?php

use Illuminate\Http\Request;
use App\Http\Middleware\AdminUser;
use App\Http\Middleware\MentorUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\MentorController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\SaveItemController;
use App\Http\Controllers\Api\FavoriteTrackController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\AdminTrackController;
use App\Http\Controllers\Dashboard\AdminMentorController;
use App\Http\Controllers\Dashboard\AdminArticleController;
use App\Http\Controllers\Dashboard\AdminSectionController;
use App\Http\Controllers\Dashboard\AdminStudentController;

// Public routes
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Contact
    Route::post('/contact', [ContactController::class, 'contact_save']);

    // Public Content
    Route::get('/sections', [SectionController::class, 'index']);
    Route::get('/sections/{slug}', [SectionController::class, 'show']);

    Route::get('/tracks', [TrackController::class, 'index']);
    Route::get('/tracks/{id}', [TrackController::class, 'show']);

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{slug}', [BlogController::class, 'show']); // لاحظ استخدام slug
});

// Authenticated routes
Route::prefix('v1')->middleware('auth:api')->group(function () {

    // User
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/update', [UserController::class, 'update']);
    Route::delete('/delete', [UserController::class, 'delete']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Favorite tracks
    Route::post('/tracks/{slug}/favorite', [FavoriteTrackController::class, 'addToFavorites']);
    Route::delete('/tracks/{slug}/favorite', [FavoriteTrackController::class, 'removeFromFavorites']);
    Route::get('/favorite-tracks', [FavoriteTrackController::class, 'getFavoriteTracks']);

    // Saved items
    Route::post('/save/article/{id}', [SaveItemController::class, 'saveArticle']);
    Route::delete('/save/article/{id}', [SaveItemController::class, 'unsaveArticle']);
    Route::post('/save/blog/{id}', [SaveItemController::class, 'saveBlog']);
    Route::delete('/save/blog/{id}', [SaveItemController::class, 'unsaveBlog']);
    Route::get('/saved', [SaveItemController::class, 'getSaved']);
});

// Mentor-only routes

Route::prefix('v1')->middleware(['auth:api', MentorUser::class])->group(function () {

    // Blogs
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{blog:slug}', [BlogController::class, 'update']);
    Route::delete('/blogs/{slug}', [BlogController::class, 'destroy']);
    Route::get('/mentor_blogs', [BlogController::class, 'mentorBlogs']);

    // Articles
    Route::post('/tracks/{track}/articles', [ArticleController::class, 'store']);
    Route::put('/articles/{slug}', [ArticleController::class, 'update']);
    Route::delete('/articles/{slug}', [ArticleController::class, 'destroy']);
    Route::get('/my-articles', [ArticleController::class, 'myArticles']);




    // Resource
    Route::post('/resources', [ResourceController::class, 'store']);
});


// Admin-only routes

Route::prefix('v1/admin')->middleware(['auth:api', AdminUser::class])->name('admin.')->group(function () {

    Route::apiResource('articles', AdminArticleController::class);
    Route::apiResource('tracks', AdminTrackController::class)->except(['index', 'show']);
    Route::put('/tracks/{slug}/publish', [AdminTrackController::class, 'publish']);
    Route::put('/tracks/{slug}/draft', [AdminTrackController::class, 'draft']);

    Route::apiResource('students', AdminStudentController::class)->only(['index', 'show', 'destroy']);
    Route::apiResource('mentors', AdminMentorController::class)->only(['index', 'show', 'destroy']);

    Route::put('/blogs/{slug}/publish', [BlogController::class, 'publish']);
    Route::put('/blogs/{slug}/reject', [BlogController::class, 'reject']);
    Route::put('/blogs/{slug}/draft', [BlogController::class, 'draft']);

    // articles
    Route::put('/articles/{slug}/accept', [ArticleController::class, 'accepted']);
    Route::put('/articles/{slug}/reject', [ArticleController::class, 'rejected']);
    Route::put('/articles/{slug}/under-review', [ArticleController::class, 'underReview']);

    // // resource
    Route::put('/resources/{slug}/accept', [ResourceController::class, 'accepted']);
    Route::put('/resources/{slug}/reject', [ResourceController::class, 'rejected']);
    Route::put('/resources/{slug}/under-review', [ResourceController::class, 'underReview']);


    Route::get('/mentor-contact', [DashboardController::class, 'mentorContents']);

    Route::post('/sections', [AdminSectionController::class, 'store']);
    Route::put('sections/{slug}', [AdminSectionController::class, 'update']);
    Route::delete('sections/{slug}', [AdminSectionController::class, 'destroy']);
    Route::put('/sections/{slug}/draft', [AdminSectionController::class, 'setDraft']);
    Route::put('/sections/{slug}/publish', [AdminSectionController::class, 'setPublished']);

    Route::get('/articles/todays-published-count', [ArticleController::class, 'getTodaysPublishedCount']);
    Route::get('/resources/this-weeks-count', [ResourceController::class, 'getThisWeeksResourceCount']);
});
