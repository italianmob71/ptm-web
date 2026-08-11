<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\EventCalendarController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TopicsController;
use App\Http\Controllers\BookController;

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