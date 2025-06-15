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
     * Dit betekent dat ze veilig via een array kunnen worden ingesteld (bijv. User::create($request->all())).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'points',
    ];

    /**
     * De attributen die verborgen moeten worden bij serialisatie (bijv. naar JSON).
     * Wachtwoorden en remember_token zijn gevoelige gegevens en hoeven niet te worden weergegeven.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * De attributen die moeten worden gecast naar specifieke PHP-typen.
     * 'email_verified_at' wordt een DateTime object, 'password' wordt automatisch gehasht.
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
     * Dit definieert een 'one-to-many' relatie, waarbij één gebruiker meerdere spellen kan initiëren.
     * Dit is nuttig om alle spellen te vinden waarbij deze gebruiker de primaire speler was.
     */
    public function gamesAsInitiator(): HasMany
    {
        return $this->hasMany(Game::class, 'user_id');
    }

    /**
     * Een gebruiker kan meerdere spellen zijn als tegenstander (als 'opponent_id').
     * Dit definieert ook een 'one-to-many' relatie, voor spellen waarbij de gebruiker de tegenstander was.
     * Dit is nuttig om alle spellen te vinden waarbij deze gebruiker de tegenspeler was.
     */
    public function gamesAsOpponent(): HasMany
    {
        return $this->hasMany(Game::class, 'opponent_id');
    }
}
