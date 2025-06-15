<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Voeg dit toe

class Game extends Model
{
    public const PLAYER_COLORS = ['Blue', 'Red'];
    public const ROWS = 6;
    public const COLUMNS = 7;

    protected $fillable = [
        'user_id',
        'opponent_id',
        'status',
        'current_player_color',
        'board_state',
    ];

    protected $casts = [
        'board_state' => 'array',
    ];

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
}
