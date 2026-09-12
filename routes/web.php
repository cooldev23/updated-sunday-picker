<?php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WeekController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserPickController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('user/{user}/league/{league}/{week?}', [WeekController::class, 'edit'])->name('edit.picks');
    Route::get('user/{user}/league/{league}/change-week', [WeekController::class, 'changeWeek'])->name('edit.change-week');

    Route::get('create-schedule', [ScheduleController::class, 'create'])->name('schedule.create');
    Route::post('create-schedule', [ScheduleController::class, 'store'])->name('schedule.store');

    Route::patch('picks/{user}/{league}', [UserPickController::class, 'setPicks'])->name('picks.update');

    Route::get('league/create', [LeagueController::class, 'create'])->name('league.create');
    Route::post('league/create', [LeagueController::class, 'store'])->name('league.store');
    Route::get('league/{league}/show-results', [LeagueController::class, 'show'])->name('league.show');
    Route::get('league/{league}/weekly-picks', [LeagueController::class, 'weeklyPicks'])->name('league.weeklyPicks');
});

Route::get('get-weekly-scores', function() {
    Artisan::call('scores:get-weekly-scores');
});

