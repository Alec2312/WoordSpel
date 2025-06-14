<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Auth\LoginRegisterController; // Zorg dat deze er is
use App\Http\Controllers\OpponentSelectionController; // Zorg dat deze er is


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
    return view('welcome'); // Of je hoofdpagina
})->name('home');

// Auth routes
Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', 'logout')->name('logout');
});

// Game routes (beschermd door auth middleware)
Route::middleware(['auth'])->group(function () {
    // Route om tegenspeler te kiezen
    Route::get('/game/select-opponent', [OpponentSelectionController::class, 'show'])->name('game.select-opponent');
    Route::post('/game/set-opponent', [OpponentSelectionController::class, 'setOpponent'])->name('game.set-opponent');

    // Hoofd spel routes
    Route::get('/game', [GameController::class, 'show'])->name('game.show');
    Route::post('/game/move', [GameController::class, 'move'])->name('game.move');
    Route::get('/game/restart', [GameController::class, 'restart'])->name('game.restart');
    Route::post('/game/reset-scores', [GameController::class, 'resetScores'])->name('game.reset-scores');

    // NIEUWE ROUTE: Bord leegmaken
    Route::post('/game/clear-board', [GameController::class, 'clearBoard'])->name('game.clear-board');
});
