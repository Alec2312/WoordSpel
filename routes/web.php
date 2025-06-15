<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\OpponentSelectionController;
use App\Http\Controllers\ProfileController; // <-- Zorg dat deze import er is!


Route::get('/', function () {
    return view('welcome'); // Of je hoofdpagina
})->name('home');

// Laadt de standaard authenticatieroutes (login, register, logout, etc.)
// Dit bestand (routes/auth.php) wordt normaal gegenereerd door Laravel Breeze/Jetstream.
require __DIR__.'/auth.php';

// Alle routes binnen deze groep vereisen dat de gebruiker is ingelogd
Route::middleware(['auth'])->group(function () {
    // Game routes
    Route::get('/game/select-opponent', [OpponentSelectionController::class, 'show'])->name('game.select-opponent');
    Route::post('/game/set-opponent', [OpponentSelectionController::class, 'setOpponent'])->name('game.set-opponent');
    Route::get('/game', [GameController::class, 'show'])->name('game.show');
    Route::post('/game/move', [GameController::class, 'move'])->name('game.move');
    Route::post('/game/reset-scores', [GameController::class, 'resetScores'])->name('game.reset-scores'); // Naam was fout, gefixt naar resetScores
    Route::post('/game/clear-board', [GameController::class, 'clearBoard'])->name('game.clear-board');

    // Profiel routes
    // Deze sectie is exact zoals jij die wilde, geplaatst binnen de 'auth' middleware groep
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
