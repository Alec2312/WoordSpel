<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GameController extends Controller
{
    /**
     * Toon het spelbord of start een nieuw spel.
     */
    public function show()
    {
        $currentUser = Auth::user();
        $selectedOpponentId = Session::get('selected_opponent_id');

        // Geen 'gast' optie meer, dus $selectedOpponentId moet altijd een User ID zijn
        if (!$selectedOpponentId) {
            // Als er geen tegenspeler is geselecteerd, stuur direct door om er een te kiezen
            session()->flash('error', 'Selecteer een tegenspeler om een spel te starten.');
            return redirect()->route('game.select-opponent');
        }

        $opponent = User::find($selectedOpponentId);
        if (!$opponent) {
            // Als de gekozen opponent niet bestaat, reset de sessie en stuur terug
            Session::forget('selected_opponent_id');
            session()->flash('error', 'De gekozen tegenspeler is niet meer beschikbaar. Kies opnieuw.');
            return redirect()->route('game.select-opponent');
        }

        $opponentTotalScore = $opponent->points;

        // Zoek of creëer een actieve game met de huidige gebruiker en de geselecteerde tegenspeler
        $game = Game::where('user_id', Auth::id())
            ->where('status', 'ongoing')
            ->where('opponent_id', $opponent->id) // Altijd een opponent_id
            ->first();

        // Als er geen actieve game is, maak dan een nieuwe aan
        if (!$game) {
            $game = $this->createNewGame(Auth::id(), $opponent->id);
        }

        // Geef de constanten voor het bord door aan de view
        $boardColumns = Game::COLUMNS;
        $boardRows = Game::ROWS;

        return view('game', compact('game', 'currentUser', 'opponent', 'opponentTotalScore', 'boardColumns', 'boardRows'));
    }

    /**
     * Verwerk een zet in het spel.
     */
    public function move(Request $request)
    {
        $maxColumn = Game::COLUMNS - 1;

        $request->validate([
            'column' => 'required|integer|min:0|max:' . $maxColumn,
            'game_id' => 'required|exists:games,id',
        ]);

        $game = Game::findOrFail($request->game_id);

        if ($game->status !== 'ongoing') {
            session()->flash('error', 'Het spel is al afgelopen. Start een nieuw spel.');
            return redirect()->route('game.show');
        }

        if (Auth::id() !== $game->user_id && Auth::id() !== $game->opponent_id) {
            session()->flash('error', 'Je bent niet geautoriseerd om in dit spel te zetten.');
            return redirect()->route('game.show');
        }

        $currentPlayerColor = $game->current_player_color;
        $board = $game->board_state ?? [];

        if (empty($board)) {
            $board = array_fill(0, Game::ROWS, array_fill(0, Game::COLUMNS, ''));
        }

        $column = $request->column;

        $placed_checker = false;
        for ($i = 0; $i < Game::ROWS; $i++) {
            if ($board[$i][$column] === '') {
                $board[$i][$column] = $currentPlayerColor;
                $placed_checker = true;
                break;
            }
        }

        if (!$placed_checker) {
            session()->flash('error', 'Kolom is vol. Kies een andere kolom.');
            return redirect()->route('game.show');
        }

        DB::transaction(function () use ($game, $board, $currentPlayerColor) {
            $game->board_state = $board;

            $isWon = $this->checkBoard($board, $currentPlayerColor);
            $resultMessage = '';
            $pointsAwarded = 1;

            if ($isWon) {
                $resultMessage = $currentPlayerColor . " heeft gewonnen!";
                $game->status = 'finished';

                if ($currentPlayerColor === 'Blue') {
                    $winner = User::find($game->user_id);
                    if ($winner) {
                        $winner->increment('points', $pointsAwarded);
                        $resultMessage .= " " . $winner->name . " krijgt " . $pointsAwarded . " punt!";
                    }
                } else {
                    $winner = User::find($game->opponent_id); // Altijd een User
                    if ($winner) {
                        $winner->increment('points', $pointsAwarded);
                        $resultMessage .= " " . $winner->name . " krijgt " . $pointsAwarded . " punt!";
                    }
                }
            } else {
                $boardIsFull = true;
                foreach ($board as $row) {
                    if (in_array('', $row)) {
                        $boardIsFull = false;
                        break;
                    }
                }

                if ($boardIsFull) {
                    $resultMessage = 'Gelijkspel!';
                    $game->status = 'tied';
                } else {
                    $game->current_player_color = ($currentPlayerColor === 'Blue') ? 'Red' : 'Blue';
                    $resultMessage = '';
                }
            }

            $game->message = $resultMessage;
            $game->save();
        });

        return redirect()->route('game.show');
    }

    /**
     * Start een nieuw spel en sla het op in de database.
     */
    public function restart()
    {
        $selectedOpponentId = Session::get('selected_opponent_id');
        if (!$selectedOpponentId) {
            session()->flash('error', 'Geen tegenspeler geselecteerd om opnieuw te starten.');
            return redirect()->route('game.select-opponent');
        }
        $this->createNewGame(Auth::id(), $selectedOpponentId);
        return redirect()->route('game.show');
    }

    /**
     * NIEUWE METHODE: Maakt het bord leeg en start een nieuw spel.
     */
    public function clearBoard()
    {
        $selectedOpponentId = Session::get('selected_opponent_id');
        if (!$selectedOpponentId) {
            session()->flash('error', 'Geen tegenspeler geselecteerd om het bord leeg te maken.');
            return redirect()->route('game.select-opponent');
        }
        $this->createNewGame(Auth::id(), $selectedOpponentId); // Dit maakt een nieuw, leeg spel aan
        session()->flash('message', 'Het bord is leeggemaakt. Een nieuw spel is gestart!');
        return redirect()->route('game.show');
    }

    /**
     * Controleert het bord op een winnende rij van vier.
     */
    private function checkBoard(array $board, string $playerColor): bool
    {
        $rows = Game::ROWS;
        $cols = Game::COLUMNS;

        // Horizontale controles
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c <= $cols - 4; $c++) {
                if (
                    $board[$r][$c] === $playerColor &&
                    $board[$r][$c+1] === $playerColor &&
                    $board[$r][$c+2] === $playerColor &&
                    $board[$r][$c+3] === $playerColor
                ) {
                    return true;
                }
            }
        }

        // Verticale controles
        for ($r = 0; $r <= $rows - 4; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if (
                    $board[$r][$c] === $playerColor &&
                    $board[$r+1][$c] === $playerColor &&
                    $board[$r+2][$c] === $playerColor &&
                    $board[$r+3][$c] === $playerColor
                ) {
                    return true;
                }
            }
        }

        // Diagonale controles (van linksonder naar rechtsboven)
        for ($r = 0; $r <= $rows - 4; $r++) {
            for ($c = 0; $c <= $cols - 4; $c++) {
                if (
                    $board[$r][$c] === $playerColor &&
                    $board[$r+1][$c+1] === $playerColor &&
                    $board[$r+2][$c+2] === $playerColor &&
                    $board[$r+3][$c+3] === $playerColor
                ) {
                    return true;
                }
            }
        }

        // Diagonale controles (van linksboven naar rechtsonder)
        for ($r = 3; $r < $rows; $r++) {
            for ($c = 0; $c <= $cols - 4; $c++) {
                if (
                    $board[$r][$c] === $playerColor &&
                    $board[$r-1][$c+1] === $playerColor &&
                    $board[$r-2][$c+2] === $playerColor &&
                    $board[$r-3][$c+3] === $playerColor
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Hulpmethode om een nieuw spel aan te maken.
     */
    private function createNewGame(int $userId, int $opponentId): Game
    {
        // Abort alle lopende games van deze gebruiker MET DEZELFDE TEGENSPELER
        Game::where('user_id', $userId)
            ->where('status', 'ongoing')
            ->where('opponent_id', $opponentId)
            ->update(['status' => 'aborted']);

        $game = new Game();
        $game->user_id = $userId;
        $game->opponent_id = $opponentId;
        $game->status = 'ongoing';
        $game->current_player_color = 'Blue';
        $game->board_state = array_fill(0, Game::ROWS, array_fill(0, Game::COLUMNS, ''));
        $game->message = '';
        $game->save();

        return $game;
    }

    /**
     * Reset de scores van de huidige gebruiker en de geselecteerde tegenspeler.
     */
    public function resetScores(Request $request)
    {
        $currentUser = Auth::user();
        $selectedOpponentId = Session::get('selected_opponent_id'); // Nodig om de opponent te vinden

        DB::transaction(function () use ($currentUser, $selectedOpponentId) {
            $currentUser->points = 0;
            $currentUser->save();

            if ($selectedOpponentId) {
                $opponent = User::find($selectedOpponentId);
                if ($opponent) {
                    $opponent->points = 0;
                    $opponent->save();
                }
            }
        });

        session()->flash('message', 'Scores zijn gereset!');
        return redirect()->route('game.show');
    }
}
