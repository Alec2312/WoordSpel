<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session; // Importeer Session

class GameController extends Controller
{
    /**
     * Toon het spelbord of start een nieuw spel.
     */
    public function show()
    {
        $game = Game::where('user_id', Auth::id())
            ->where('status', 'ongoing')
            ->first();

        if (!$game) {
            $game = $this->createNewGame(Auth::id());
        }

        // Zorg ervoor dat $currentUser de meest recente data bevat
        $currentUser = Auth::user();

        // Haal de gastscore op uit de sessie, standaard 0
        $guestTotalScore = Session::get('guest_total_score', 0);

        return view('game', compact('game', 'currentUser', 'guestTotalScore')); // Geef guestTotalScore mee aan de view
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

        $currentPlayerColor = $game->current_player_color;
        $board = $game->board_state ?? [];

        if (empty($board)) {
            $board = array_fill(0, Game::ROWS, array_fill(0, Game::COLUMNS, ''));
        }

        $column = $request->column;

        $placed_checker = false;
        // Vind de laagste lege rij in de gekozen kolom
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

                if ($currentPlayerColor === 'Blue') { // Speler 1 (ingelogde gebruiker) heeft gewonnen
                    $winner = User::find($game->user_id);
                    if ($winner) {
                        $winner->increment('points', $pointsAwarded);
                        $resultMessage .= " " . $winner->name . " krijgt " . $pointsAwarded . " punt!";
                    }
                } else { // Speler 2 (gastspeler) heeft gewonnen
                    // Increment de guest_total_score in de sessie (niet persistent over sessies)
                    $currentGuestScore = Session::get('guest_total_score', 0);
                    Session::put('guest_total_score', $currentGuestScore + $pointsAwarded);

                    $resultMessage .= " Speler 2 (Rood) krijgt " . $pointsAwarded . " punt!";
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
     * Start een nieuw spel en sla het op in de database.
     */
    public function restart()
    {
        $this->createNewGame(Auth::id());
        return redirect()->route('game.show');
    }

    /**
     * Hulpmethode om een nieuw spel aan te maken.
     */
    private function createNewGame(int $userId): Game
    {
        // Abort alle lopende games van deze gebruiker
        Game::where('user_id', $userId)
            ->where('status', 'ongoing')
            ->update(['status' => 'aborted']);

        $game = new Game();
        $game->user_id = $userId;
        $game->status = 'ongoing';
        $game->current_player_color = 'Blue';
        $game->board_state = array_fill(0, Game::ROWS, array_fill(0, Game::COLUMNS, ''));
        $game->message = '';
        $game->guest_player_score = 0; // Deze blijft 0, want dit is de score voor DIT SPECIFIEKE SPEL
        $game->save();

        return $game;
    }
}
