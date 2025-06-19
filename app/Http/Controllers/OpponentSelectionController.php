<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Game; // Make sure to import the Game model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OpponentSelectionController extends Controller
{
    /**
     * Display the opponent selection page.
     */
    public function show()
    {
        // Fetch all users except the currently authenticated user
        $users = User::where('id', '!=', Auth::id())->get();

        return view('select-opponent', compact('users'));
    }

    /**
     * Process the chosen opponent and potentially start a new game.
     */
    public function setOpponent(Request $request)
    {
        $request->validate([
            'opponent_id' => 'required|exists:users,id',
        ]);

        $opponentId = (int) $request->opponent_id; // Cast to int for strict comparison

        if ($opponentId === Auth::id()) {
            // Error message for not playing against yourself. This is a validation error,
            // so `withErrors` remains appropriate here.
            return back()->withErrors(['opponent_id' => 'Je kunt niet tegen jezelf spelen.']);
        }

        $currentSelectedOpponentId = Session::get('selected_opponent_id');

        // Store the chosen opponent ID in the session
        Session::put('selected_opponent_id', $opponentId);

        // IF THE OPPONENT CHANGES, RESET BOARD AND SCORES
        if ($currentSelectedOpponentId !== $opponentId) {
            /** @var User $currentUser */
            $currentUser = Auth::user();

            // Reset points for the current user using the method in the User model
            $currentUser->resetPoints();

            // Fetch the opponent to reset their points
            /** @var User|null $opponent */
            $opponent = User::find($opponentId);
            $opponent?->resetPoints(); // Use null-safe operator if opponent might be null

            // Create a new game (clear board) using the static method in the Game model
            Game::resetAndStartNewGame($currentUser->id, $opponentId);
        }

        // Redirect to the game board
        return redirect()->route('game.show');
    }
}
