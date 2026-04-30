<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\NovelController;
use App\Http\Controllers\DashboardController;

// ──────────────────────────────────────────
// PUBLIC
// ──────────────────────────────────────────
Route::get("/", function () {
    return Inertia::render('LandingPage');
})->name('landing-page');

Route::get("/home", [NovelController::class, 'home'])->name('home');

Route::get("/detail/{id}", [NovelController::class, 'detail'])->name('detail');

Route::get("/content/{id}/", [NovelController::class, 'content'])->name('content');

// Counters (public — called via fetch, no Inertia redirect)
Route::post('/novel/{id}/read', [NovelController::class, 'incrementRead'])->name('novel.read');
Route::post('/novel/{id}/download', [NovelController::class, 'incrementDownload'])->name('novel.download');

// ──────────────────────────────────────────
// AUTH REQUIRED (any role)
// ──────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user_dashboard'])->name('dashboard');

    Route::post('/comment', [NovelController::class, 'post_comment'])->name('comment');

    // Like / Star toggles
    Route::post('/novel/{id}/like', [NovelController::class, 'toggleLike'])->name('novel.like');
    Route::post('/novel/{id}/star', [NovelController::class, 'toggleStar'])->name('novel.star');
});

// ──────────────────────────────────────────
// AUTHOR
// ──────────────────────────────────────────
Route::middleware(['auth', 'check_author'])->prefix('author')->name('author.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'author_dashboard'])->name('dashboard');

    // Novel CRUD
    Route::post('/novel/upload', [NovelController::class, 'post_novel'])->name('novel.upload');
    Route::patch('/novel/{id}', [DashboardController::class, 'author_update_novel'])->name('novel.update');
    Route::delete('/novel/{id}', [DashboardController::class, 'author_delete_novel'])->name('novel.delete');

    // Volume / Chapter
    Route::get('/novel/{id}/add', [DashboardController::class, 'addVolumesOrChapters'])->name('add');
    Route::post('/novel/{id}/volume', [DashboardController::class, 'storeVolume'])->name('volume.store');
    Route::post('/volume/{id}/chapter', [DashboardController::class, 'storeChapter'])->name('chapter.store');

    Route::patch('/volume/{id}', [DashboardController::class, 'updateVolume'])->name('volume.update');
    Route::delete('/volume/{id}', [DashboardController::class, 'deleteVolume'])->name('volume.delete');

    Route::patch('/chapter/{id}', [DashboardController::class, 'updateChapter'])->name('chapter.update');
    Route::delete('/chapter/{id}', [DashboardController::class, 'deleteChapter'])->name('chapter.delete');
});

// Keep old route name alias for backward-compat links
Route::get('/author/add/{id}', [DashboardController::class, 'addVolumesOrChapters'])
    ->middleware(['auth', 'check_author'])
    ->name('addVolumesOrChapters');

// Old upload route alias (FormCreateNovel posts here)
Route::post('/upload_novel', [NovelController::class, 'post_novel'])->name('post_novel');

// ──────────────────────────────────────────
// ADMIN
// ──────────────────────────────────────────
Route::middleware(['auth', 'check_admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'admin_dashboard'])->name('dashboard');

    Route::patch('/novel/{id}', [DashboardController::class, 'admin_update_novel'])->name('novel.update');
    Route::delete('/novel/{id}', [DashboardController::class, 'admin_delete_novel'])->name('novel.delete');

    // Carousels
    Route::post('/carousel', [DashboardController::class, 'admin_store_carousel'])->name('carousel.store');
    Route::delete('/carousel/{id}', [DashboardController::class, 'admin_delete_carousel'])->name('carousel.delete');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
