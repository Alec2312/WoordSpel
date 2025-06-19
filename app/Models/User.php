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
     * De attributen die mass-assignable zijn.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'points', // Zorg ervoor dat 'points' hierin staat
    ];

    /**
     * De attributen die verborgen moeten worden bij serialisatie.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * De attributen die moeten worden gecast naar specifieke PHP-typen.
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

    /**
     * **DEZE METHODE IS HIER BELANGRIJK!**
     * Reset de punten van de gebruiker naar 0.
     */
    public function resetPoints(): void
    {
        $this->points = 0;
        $this->save();
    }
}
