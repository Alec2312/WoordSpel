<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'points', // 'points' is hier toegestaan
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Een gebruiker kan meerdere GamePlayer records hebben.
     */
    public function gamePlayers(): HasMany
    {
        return $this->hasMany(GamePlayer::class);
    }

    /**
     * Een gebruiker kan meerdere Cell records hebben.
     */
    public function cells(): HasMany
    {
        return $this->hasMany(Cell::class);
    }

    /**
     * Een gebruiker kan meerdere spellen gewonnen hebben (als de 'winner_id' van het spel).
     */
    public function gamesWon(): HasMany
    {
        return $this->hasMany(Game::class, 'winner_id');
    }

    /**
     * Een gebruiker kan meerdere spellen zijn gestart (als 'user_id').
     */
    public function gamesAsInitiator(): HasMany
    {
        return $this->hasMany(Game::class, 'user_id');
    }

    /**
     * Een gebruiker kan meerdere spellen zijn als tegenstander (als 'opponent_id').
     */
    public function gamesAsOpponent(): HasMany
    {
        return $this->hasMany(Game::class, 'opponent_id');
    }

    // `gamesInTurn` relatie is niet direct bruikbaar met `current_player_color` (string),
    // tenzij je een `current_turn_user_id` kolom zou toevoegen. Voor nu uitgeschakeld.
    /*
    public function gamesInTurn(): HasMany
    {
        return $this->hasMany(Game::class, 'current_turn');
    }
    */
}
