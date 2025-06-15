<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View; // Nodig voor return type declaratie
use Illuminate\Http\RedirectResponse; // Nodig voor return type declaratie

class GameController extends Controller
{
    /**
     * Toon het spelbord of start een nieuw spel.
     */
    public function show(): View|RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $selectedOpponentId = Session::get('selected_opponent_id');

        /** @var User|null $opponent */
        $opponent = User::find($selectedOpponentId);

        if (!$opponent) {
            Session::forget('selected_opponent_id');
            return redirect()->route('game.select-opponent');
        }

        $opponentTotalScore = $opponent->points;

        $game = Game::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'opponent_id' => $opponent->id,
                'status' => 'ongoing'
            ],
            [
                'board_state' => array_fill(0, Game::ROWS, array_fill(0, Game::COLUMNS, '')),
                'current_player_color' => 'Blue',
                'message' => ''
            ]
        );

        Game::where('user_id', Auth::id())
            ->where('opponent_id', $opponent->id)
            ->where('status', 'ongoing')
            ->where('id', '!=', $game->id)
            ->update(['status' => 'aborted']);

        $boardColumns = Game::COLUMNS;
        $boardRows = Game::ROWS;

        // DEFINIEER EN GEEF $boardState NU DIRECT MEE VANUIT DE CONTROLLER
        $boardState = $game->board_state ?? array_fill(0, Game::ROWS, array_fill(0, Game::COLUMNS, ''));

        return view('game', compact('game', 'currentUser', 'opponent', 'opponentTotalScore', 'boardColumns', 'boardRows', 'boardState'));
    }

    /**
     * Verwerk een zet in het spel.
     */
    public function move(Request $request): RedirectResponse
    {
        // Valideer de inkomende request data
        $request->validate([
            'column' => 'required|integer|min:0|max:' . (Game::COLUMNS - 1),
            'game_id' => 'required|exists:games,id',
        ]);

        $game = Game::findOrFail($request->game_id);

        // Spelstatus check: Alleen zetten in een "ongoing" spel toestaan
        if ($game->status !== 'ongoing') {
            return redirect()->route('game.show');
        }

        // Autorisatiecheck: Controleer of de ingelogde gebruiker deel is van dit specifieke spel
        if (Auth::id() !== $game->user_id && Auth::id() !== $game->opponent_id) {
            return redirect()->route('game.show');
        }

        $currentPlayerColor = $game->current_player_color;
        // Initialiseer $board als leeg als $game->board_state null is
        $board = $game->board_state ?? array_fill(0, Game::ROWS, array_fill(0, Game::COLUMNS, ''));

        $column = $request->column;

        // Probeer een fiche te plaatsen met de hulpmethode
        $placedRow = $this->placeChecker($board, $column, $currentPlayerColor);

        // Redirect als de kolom vol is (geen fiche geplaatst)
        if ($placedRow === false) { // placeChecker retourneert false als kolom vol is
            return redirect()->route('game.show');
        }

        DB::transaction(function () use ($game, $board, $currentPlayerColor) {
            $game->board_state = $board;
            $game->message = ''; // Leeg de message kolom

            // Controleer op winst
            $pointsAwarded = 1;
            if ($this->checkWin($board, $currentPlayerColor)) {
                $game->status = 'finished';
                // Bepaal de winnaar en ken punten toe
                /** @var User|null $winner */
                $winner = ($currentPlayerColor === 'Blue') ? User::find($game->user_id) : User::find($game->opponent_id);
                $winner?->increment('points', $pointsAwarded);
            } elseif ($this->checkDraw($board)) { // Controleer op gelijkspel (bord vol)
                $game->status = 'tied';
            } else {
                // Spel gaat verder, wissel van beurt
                $game->current_player_color = ($currentPlayerColor === 'Blue') ? 'Red' : 'Blue';
            }

            $game->save();
        });

        return redirect()->route('game.show');
    }

    /**
     * Maakt het bord leeg en start een nieuw spel.
     */
    public function clearBoard(): RedirectResponse
    {
        $selectedOpponentId = Session::get('selected_opponent_id');

        // Aborteer de huidige 'ongoing' game van deze specifieke gebruiker met deze tegenspeler.
        // Dit heeft geen effect als $selectedOpponentId null is, wat veilig is.
        Game::where('user_id', Auth::id())
            ->where('opponent_id', $selectedOpponentId)
            ->where('status', 'ongoing')
            ->update(['status' => 'aborted']);

        // Maak een gloednieuw spel aan. Let op: 'opponent_id' moet een geldige waarde hebben.
        // De 'show()' methode dwingt dit af voordat de gebruiker de spelpagina kan bereiken.
        Game::create([
            'user_id' => Auth::id(),
            'opponent_id' => $selectedOpponentId,
            'status' => 'ongoing',
            'current_player_color' => 'Blue',
            'board_state' => array_fill(0, Game::ROWS, array_fill(0, Game::COLUMNS, '')),
            'message' => ''
        ]);

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

        DB::transaction(function () use ($currentUser, $selectedOpponentId) {
            $currentUser->points = 0;
            $currentUser->save();

            if ($selectedOpponentId) {
                /** @var User|null $opponent */
                $opponent = User::find($selectedOpponentId);
                if ($opponent) {
                    $opponent->points = 0;
                    $opponent->save();
                }
            }
        });

        return redirect()->route('game.show');
    }

    /**
     * Hulpmethode: Plaats een fiche in de opgegeven kolom.
     *
     * @param array $board De huidige bordstatus
     * @param int $column De kolom waar het fiche geplaatst moet worden
     * @param string $playerColor De kleur van de speler
     * @return int|bool De rij waar het fiche is geplaatst, of false als de kolom vol is.
     */
    private function placeChecker(array &$board, int $column, string $playerColor): int|bool
    {
        for ($i = 0; $i < Game::ROWS; $i++) {
            if ($board[$i][$column] === '') {
                $board[$i][$column] = $playerColor;
                return $i;
            }
        }
        return false;
    }

    /**
     * Hulpmethode: Controleert het bord op een winnende rij van vier.
     */
    private function checkWin(array $board, string $playerColor): bool
    {
        $rows = Game::ROWS;
        $cols = Game::COLUMNS;

        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if ($board[$r][$c] === $playerColor) {
                    // Horizontale check
                    if ($c + 3 < $cols && $board[$r][$c+1] === $playerColor && $board[$r][$c+2] === $playerColor && $board[$r][$c+3] === $playerColor) {
                        return true;
                    }
                    // Verticale check
                    if ($r + 3 < $rows && $board[$r+1][$c] === $playerColor && $board[$r+2][$c] === $playerColor && $board[$r+3][$c] === $playerColor) {
                        return true;
                    }
                    // Diagonale check (rechtsboven naar linksonder)
                    if ($r + 3 < $rows && $c + 3 < $cols && $board[$r+1][$c+1] === $playerColor && $board[$r+2][$c+2] === $playerColor && $board[$r+3][$c+3] === $playerColor) {
                        return true;
                    }
                    // Diagonale check (rechtsonder naar linksboven)
                    if ($r - 3 >= 0 && $c + 3 < $cols && $board[$r-1][$c+1] === $playerColor && $board[$r-2][$c+2] === $playerColor && $board[$r-3][$c+3] === $playerColor) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Hulpmethode: Controleert of het bord vol is (gelijkspel).
     */
    private function checkDraw(array $board): bool
    {
        foreach ($board as $row) {
            if (in_array('', $row)) {
                return false; // Er is nog een lege plek
            }
        }
        return true; // Bord is vol
    }
}
