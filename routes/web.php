<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Connect Four Spelroutes
    Route::get('/game', [GameController::class, 'show'])->name('game.show');
    // De move route moet een POST zijn om de formulierdata te ontvangen
    Route::post('/game/move', [GameController::class, 'move'])->name('game.move');
    Route::get('/game/restart', [GameController::class, 'restart'])->name('game.restart');
});

require __DIR__.'/auth.php';
