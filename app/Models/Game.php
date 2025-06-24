<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    public const ROWS = 6;
    public const COLUMNS = 7;

    protected $fillable = [
        'user_id',
        'opponent_id',
        'status',
        'current_player_color',
        'board_state',
        'message',
    ];

    protected $casts = [
        'board_state' => 'array',
    ];
    // de array board_state is een json of text en dat wordt hierdoor omgezet naar een php array

    /**
     * Relatie: Een spel behoort toe aan een gebruiker (Speler 1).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relatie: Een spel kan een tegenstander hebben (Speler 2).
     */
    public function opponent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opponent_id');
    }

    /**
     * Vindt een lopend spel tussen twee spelers of start een nieuw spel.
     * Aborteert ook andere lopende spellen tussen dezelfde spelers.
     * Dit is een methode die database queries uitvoert.
     */
    public static function findOrCreateGame(int $userId, int $opponentId): self
    {
        // Zoek een lopend spel
        $game = self::firstOrCreate(
        // zoekt naar een methode die deze attributen matcht en als er geen is gevonden dan wordt er een
        // aangemaakt nadat een attribuut uit de tweede parameters wordt toegepast
            [
                'user_id' => $userId,
                'opponent_id' => $opponentId,
                'status' => 'ongoing'
            ],
            [
                'board_state' => array_fill(0, self::ROWS, array_fill(0, self::COLUMNS, '')),
                'current_player_color' => 'Blue',
                'message' => ''
            ]
        // dit kijkt of er een spel gaande is en als er een spel gaande is onthoud hij de attributen die
        // weer worden weergeven als het spel hervatten wordt
        );

        // Aborteer andere lopende spellen tussen dezelfde spelers
        self::where('user_id', $userId)
            // self kijkt naar de huidige klasse
            ->where('opponent_id', $opponentId)
            ->where('status', 'ongoing')
            ->where('id', '!=', $game->id)
            ->update(['status' => 'aborted']);

        return $game;
        // zoekt naar andere ongoing spellen tussen dezelfde speler die niet van hierboven
        // zijn en zet de status van die game op aborted.
        // zo kunnen spelers niet meerdere spellen met dezelfde speler spelen
    }

    /**
     * Maakt het bord leeg en start een nieuw spel tussen de opgegeven spelers.
     * Dit is een methode die database queries uitvoert.
     */
    public static function resetAndStartNewGame(int $userId, int $opponentId): self
    {
        // Aborteer de huidige 'ongoing' game van deze specifieke gebruiker met deze tegenspeler.
        self::where('user_id', $userId)
            ->where('opponent_id', $opponentId)
            ->where('status', 'ongoing')
            ->update(['status' => 'aborted']);

        // Maak een gloednieuw spel aan.
        return self::create([
            'user_id' => $userId,
            'opponent_id' => $opponentId,
            'status' => 'ongoing',
            'current_player_color' => 'Blue',
            'board_state' => array_fill(0, self::ROWS, array_fill(0, self::COLUMNS, '')),
            'message' => ''
        ]);
    }

    // De processMove en de helper methoden (placeChecker, checkWin, checkDraw)
    // zijn terugverplaatst naar de GameController, omdat ze de bordlogica behandelen
    // en niet primair database queries zijn.
    // De queries voor User (find, increment) die in processMove stonden, zijn nu direct
    // in de GameController opgenomen, zoals gevraagd.
}
