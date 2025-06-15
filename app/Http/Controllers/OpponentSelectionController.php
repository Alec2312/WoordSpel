<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GameController; // Importeer de GameController

class OpponentSelectionController extends Controller
{
    /**
     * Toon de pagina voor het kiezen van een tegenspeler.
     */
    public function show()
    {
        // Haal alle gebruikers op behalve de ingelogde gebruiker zelf
        $users = User::where('id', '!=', Auth::id())->get();

        return view('select-opponent', compact('users'));
    }

    /**
     * Verwerk de gekozen tegenspeler en start een nieuw spel.
     */
    public function setOpponent(Request $request)
    {
        $request->validate([
            'opponent_id' => 'required|exists:users,id',
        ]);

        $opponentId = $request->opponent_id;

        if ($opponentId == Auth::id()) {
            // Foutmelding voor niet tegen jezelf spelen. Deze is een validatiefout,
            // dus de `withErrors` blijft staan, aangezien je deze validatie wilt.
            return back()->withErrors(['opponent_id' => 'Je kunt niet tegen jezelf spelen.']);
        }

        // Krijg de huidige geselecteerde tegenstander voor vergelijking
        $currentSelectedOpponentId = Session::get('selected_opponent_id');

        // Sla de gekozen tegenspeler-ID op in de sessie
        Session::put('selected_opponent_id', $opponentId);

        // ALS DE TEGENSTANDER VERANDERT, RESET DAN BORD EN SCORES
        if ($currentSelectedOpponentId !== (int)$opponentId) { // Cast naar int voor veilige vergelijking
            $gameController = new GameController();

            // Reset de scores van BEIDE spelers
            // Hier moet de $request verwijderd worden, want resetScores verwacht deze niet meer.
            $gameController->resetScores(); // <-- Aangepast!

            // Maak een nieuw spel (bord leeg). clearBoard zal automatisch een nieuw spel starten
            $gameController->clearBoard();
        }

        // Redirect naar het spelbord
        return redirect()->route('game.show');
    }
}
