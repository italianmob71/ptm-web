<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\TeamController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::get('/about', function () {
    return view('about.index', ['title' => 'About']);
})->name('about');
Route::get('/resources', function () {
    return view('resources.index', ['title' => 'Resources']);
})->name('resources');