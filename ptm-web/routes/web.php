<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\EventCalendarController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventCalendarController::class, 'index'])->name('events');
Route::get('/resources', function () {
    return view('resources.index', ['title' => 'Resources']);
})->name('resources');