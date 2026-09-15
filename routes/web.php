<?php

use App\Http\Controllers\Admin\NFLDataController;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\WeekController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserPickController;

// auth routes
require __DIR__.'/settings.php';

// welcome
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// after login
Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('user')->name('user.')->group(function() {
        Route::get('/{user}/league/{league}/{week?}', [WeekController::class, 'edit'])->name('edit.picks');
        Route::get('/{user}/league/{league}/change-week', [WeekController::class, 'changeWeek'])->name('edit.change-week');
    });

    Route::prefix('picks')->name('picks.')->group(function() {
        Route::patch('/{user}/{league}', [UserPickController::class, 'setPicks'])->name('update');
    });

    Route::prefix('league')->name('league.')->group(function() {
        Route::get('/create', [LeagueController::class, 'create'])->name('create');
        Route::post('/create', [LeagueController::class, 'store'])->name('store');
        Route::get('/{league}/show-results', [LeagueController::class, 'show'])->name('show');
        Route::get('/{league}/weekly-picks', [LeagueController::class, 'weeklyPicks'])->name('weeklyPicks');
    });

    Route::middleware(['role:Super Admin'])->prefix('admin')->name('admin.')->group(function() {
        Route::get('/get-nfl-data', [NFLDataController::class, 'index'])->name('get-nfl-data');
        // Route::prefix('nfl-data')->name('nfl-data.')->group(function() {
        //     Route::get('/create', [ScheduleController::class, 'create'])->name('create');
        //     Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        // });
    });
});

// command testing
Route::get('get-weekly-scores', function() {
    Artisan::call('scores:get-weekly-scores');
});

