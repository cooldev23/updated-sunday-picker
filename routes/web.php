<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NFLDataController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\TeamsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\UserPickController;
use App\Http\Controllers\WeekController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// registration
Route::prefix('otp')->name('otp.')->group(function() {
    Route::get('/register/{league}/{code}/{email}', [OtpController::class , 'register'])->name('register');
    Route::post('/register/{league}/{code}/{email}', [OtpController::class , 'store'])->name('store');
});

// after login
Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('user')->name('user.')->group(function() {
        Route::get('/{user}/league/{league}/{week?}', [WeekController::class, 'edit'])->name('edit.picks');
        Route::get('/{user}/league/{league}/change-week', [WeekController::class, 'changeWeek'])->name('edit.changeWeek');
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
        Route::get('/dashboard', [AdminController::class, 'index'])->name('adminDashboard');
        Route::get('/get-nfl-data', [NFLDataController::class, 'index'])->name('getNflData');
        Route::get('/nfl-schedule/store', ScheduleController::class)->name('nflSchedule.store');
        Route::get('/nfl-teams/store', TeamsController::class)->name('nflTeams.storeTeams');
    });
});

// command testing
Route::get('get-weekly-scores', function() {
    Artisan::call('scores:get-weekly-scores');
});
Route::get('get-current-week', function() {
    Artisan::call('schedule:get-current-week');
});

