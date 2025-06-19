<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB; // Nodig voor DB::transaction

class GameController extends Controller
{
    // Constant waarden die nodig zijn voor de bordlogica, nu hier gedefinieerd
    private const ROWS = 6;
    private const COLUMNS = 7;
    // de grootte van het speelveld, const zodat de waarde niet veranderd kan worden

    /**
     * Toon het spelbord of start een nieuw spel.
     */
    public function show(): View|RedirectResponse
    {
        $currentUser = Auth::user(); // haalt ingelogd user op
        $selectedOpponentId = Session::get('selected_opponent_id'); // haalt id van gekozen tegenstanders op

        // Controleer of een tegenspeler is geselecteerd
        if (!$selectedOpponentId) {
            return redirect()->route('game.select-opponent');
        }

        $opponent = User::find($selectedOpponentId);

        // Als de tegenspeler niet gevonden wordt, leeg de sessie en stuur terug
        if (!$opponent) {
            Session::forget('selected_opponent_id');
            return redirect()->route('game.select-opponent');
        }

        // Gebruik de methode in het Game model om het spel te vinden of te starten
        $game = Game::findOrCreateGame($currentUser->id, $opponent->id);

        $boardColumns = self::COLUMNS;
        $boardRows = self::ROWS;
        // gebruik van de controller constants
        $boardState = $game->board_state;

        return view('game', compact('game', 'currentUser', 'opponent', 'boardColumns', 'boardRows', 'boardState'));
    }

    /**
     * Verwerk een zet in het spel.
     */
    public function move(Request $request): RedirectResponse
    {
        $request->validate([
            'column' => 'required|integer|min:0|max:' . (self::COLUMNS - 1), // kolommen gaan van 0 tot 6
            'game_id' => 'required|exists:games,id',
        ]);
        // kijken of de inkomende data voldoet aan de eisen hierboven

        $game = Game::findOrFail($request->game_id);
        // probeert een game record te vinden op basis van game_id en geeft anders een fout

        // controleert of de ingelogde gebruiker deel is van dit specifieke spel
        if (Auth::id() !== $game->user_id && Auth::id() !== $game->opponent_id) {
            return redirect()->route('game.show');
        }

        // je mag alleen een zet zetten als een speel "ongoing" is
        if ($game->status !== 'ongoing') {
            return redirect()->route('game.show');
        }

        $column = $request->column;

        // De logica voor processMove en de helperfuncties zijn nu hier in de controller
        return DB::transaction(function () use ($column, $game) {
            // alle database acties slagen in een keer of mislukken
            $board = $game->board_state;
            $currentPlayerColor = $game->current_player_color;
            // haalt huidige board_state en current_player_color op van de game instantie

            // Probeer een fiche te plaatsen
            $placedRow = $this->placeChecker($board, $column, $currentPlayerColor);

            if ($placedRow === false) {
                return redirect()->route('game.show')->with('error', 'Kolom is vol!');
            }

            $game->board_state = $board;
            $game->message = '';
            // past het bord aan met de nieuwe ingevulde plek

            $pointsAwarded = 1;
            // je krijgt 1 punt per ronde die je wint

            if ($this->checkWin($board, $currentPlayerColor)) {
                $game->status = 'finished';
                // Bepaal de winnaar en ken punten toe
                $winnerId = ($currentPlayerColor === 'Blue') ? $game->user_id : $game->opponent_id;
                /** @var User|null $winner */
                $winner = User::find($winnerId); // Dit is een query
                $winner?->increment('points', $pointsAwarded); // Dit is een query
                // increment is om het aantal punten van de winnaar te verhogen
            } elseif ($this->checkDraw($board)) {
                $game->status = 'tied';
            } else {
                // Spel gaat verder, wissel van beurt
                $game->current_player_color = ($currentPlayerColor === 'Blue') ? 'Red' : 'Blue';
            }

            $game->save(); // Dit is een query: slaat de gewijzigde game status op in de database
            return redirect()->route('game.show');
        });
    }

    /**
     * Maakt het bord leeg en start een nieuw spel.
     */
    public function clearBoard(): RedirectResponse
    {
        $selectedOpponentId = Session::get('selected_opponent_id');
        $currentUser = Auth::user();

        // Roep de methode in het Gamemodel aan (bevat queries)
        Game::resetAndStartNewGame($currentUser->id, $selectedOpponentId);

        return redirect()->route('game.show');
    }

    /**
     * Reset de scores van de huidige gebruiker en de geselecteerde tegenspeler.
     */
    public function resetScores(): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $selectedOpponentId = Session::get('selected_opponent_id');

        // Roep de methode in het User model aan om scores te resetten (bevat queries)
        $currentUser->resetPoints();

        if ($selectedOpponentId) {
            /** @var User|null $opponent */
            $opponent = User::find($selectedOpponentId); // Dit is een query
            $opponent?->resetPoints(); // Bevat queries
        }

        return redirect()->route('game.show');
    }

    /**
     * Hulpmethode: Plaats een fiche in de opgegeven kolom.
     * Dit blijft in de controller omdat het geen database query is, maar bordlogica.
     *
     * @param array $board De huidige bordstatus (wordt per referentie aangepast)
     * @param int $column De kolom waar het fiche geplaatst moet worden
     * @param string $playerColor De kleur van de speler
     * @return int|bool De rij waar het fiche is geplaatst, of false als de kolom vol is.
     */
    private function placeChecker(array &$board, int $column, string $playerColor): int|bool
    {
        for ($i = 0; $i < self::ROWS; $i++) {
            // $i is de onderste rij
            if ($board[$i][$column] === '') {
                $board[$i][$column] = $playerColor;
                return $i;
            }
            // de eerste lege plek die hij vindt in het bord wordt gevuld door de player color
            // geeft daarna het rijnummer terug
            // als de hele rijd vol is, dan geeft hij false terug
        }
        return false;
    }

    /**
     * Hulpmethode: Controleert het bord op een winnende rij van vier.
     * Dit blijft in de controller omdat het geen database query is, maar bordlogica.
     */
    private function checkWin(array $board, string $playerColor): bool
    {
        $rows = self::ROWS;
        $cols = self::COLUMNS;

        // controleert horizontaal, verticaal en diagonaal of een speler gewonnen heeft
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if ($board[$r][$c] === $playerColor) {
                    // Horizontale check
                    if ($c + 3 < $cols && $board[$r][$c + 1] === $playerColor && $board[$r][$c + 2] === $playerColor && $board[$r][$c + 3] === $playerColor) {
                        return true;
                    }
                    // Verticale check
                    if ($r + 3 < $rows && $board[$r + 1][$c] === $playerColor && $board[$r + 2][$c] === $playerColor && $board[$r + 3][$c] === $playerColor) {
                        return true;
                    }
                    // Diagonale check (rechtsboven naar linksonder)
                    if ($r + 3 < $rows && $c + 3 < $cols && $board[$r + 1][$c + 1] === $playerColor && $board[$r + 2][$c + 2] === $playerColor && $board[$r + 3][$c + 3] === $playerColor) {
                        return true;
                    }
                    // Diagonale check (rechtsonder naar linksboven)
                    if ($r - 3 >= 0 && $c + 3 < $cols && $board[$r - 1][$c + 1] === $playerColor && $board[$r - 2][$c + 2] === $playerColor && $board[$r - 3][$c + 3] === $playerColor) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Hulpmethode: Controleert of het bord vol is (gelijkspel).
     * Dit blijft in de controller omdat het geen database query is, maar bordlogica.
     */
    private function checkDraw(array $board): bool
    {
        foreach ($board as $row) {
            if (in_array('', $row)) {
                return false;
            }
        }
        return true;
        // dit kijkt of er nog lege plekken op het bord zijn
        // en als er geen lege plekken zijn en er geen winnaar is dan is het gelijkspel
    }
}
