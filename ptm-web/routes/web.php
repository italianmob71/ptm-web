<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\EventCalendarController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TopicsController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookAdminController;
use App\Http\Controllers\AuthorAdminController;
use App\Http\Controllers\BlogPostAdminController;
use App\Http\Controllers\ImageAdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordUpdateController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventCalendarController::class, 'index'])->name('events');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/topics', [TopicsController::class, 'index'])->name('topics.index');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');
Route::get('/resources', function () {
    return view('resources.index', ['title' => 'Resources']);
})->name('resources');

// Auth routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Password update routes (for first-time login)
Route::get('/password/update', [PasswordUpdateController::class, 'create'])->name('password.update.form');
Route::post('/password/update', [PasswordUpdateController::class, 'store'])->name('password.update');

// CKEditor image endpoints (auth required)
Route::get('/admin/images/search', [ImageAdminController::class, 'search'])
    ->middleware(['auth', 'level:9'])->name('admin.images.search');
Route::post('/admin/images/ckeditor-upload', [ImageAdminController::class, 'ckeditorUpload'])
    ->middleware(['auth', 'level:9'])->name('admin.images.ckeditor');

// Admin routes — super-admin only (security level 9)
Route::middleware(['auth', 'level:9'])->prefix('admin')->name('admin.')->group(function () {
    // Books
    Route::get('/books', [BookAdminController::class, 'index'])->name('books.index');
    Route::get('/books/create', [BookAdminController::class, 'create'])->name('books.create');
    Route::post('/books', [BookAdminController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookAdminController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookAdminController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookAdminController::class, 'destroy'])->name('books.destroy');

    // Authors
    Route::get('/authors', [AuthorAdminController::class, 'index'])->name('authors.index');
    Route::get('/authors/create', [AuthorAdminController::class, 'create'])->name('authors.create');
    Route::post('/authors', [AuthorAdminController::class, 'store'])->name('authors.store');
    Route::get('/authors/{author}/edit', [AuthorAdminController::class, 'edit'])->name('authors.edit');
    Route::put('/authors/{author}', [AuthorAdminController::class, 'update'])->name('authors.update');
    Route::delete('/authors/{author}', [AuthorAdminController::class, 'destroy'])->name('authors.destroy');

    // Blog Posts
    Route::get('/blog', [BlogPostAdminController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [BlogPostAdminController::class, 'create'])->name('blog.create');
    Route::post('/blog', [BlogPostAdminController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}/edit', [BlogPostAdminController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [BlogPostAdminController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [BlogPostAdminController::class, 'destroy'])->name('blog.destroy');

    // Images
    Route::get('/images', [ImageAdminController::class, 'index'])->name('images.index');
    Route::get('/images/create', [ImageAdminController::class, 'create'])->name('images.create');
    Route::post('/images', [ImageAdminController::class, 'store'])->name('images.store');
    Route::get('/images/{image}/edit', [ImageAdminController::class, 'edit'])->name('images.edit');
    Route::put('/images/{image}', [ImageAdminController::class, 'update'])->name('images.update');
    Route::delete('/images/{image}', [ImageAdminController::class, 'destroy'])->name('images.destroy');
});
