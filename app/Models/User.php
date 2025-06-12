<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Voeg 'profile' toe hier:
    protected $fillable = ['name', 'email', 'password', 'profile'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function gamePlayers()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function cells()
    {
        return $this->hasMany(Cell::class);
    }

    public function gamesWon()
    {
        return $this->hasMany(Game::class, 'winner_id');
    }

    public function gamesInTurn()
    {
        return $this->hasMany(Game::class, 'current_turn');
    }
}
