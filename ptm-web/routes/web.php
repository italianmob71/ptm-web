<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\EventCalendarController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TopicsController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordUpdateController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventCalendarController::class, 'index'])->name('events');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
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
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');