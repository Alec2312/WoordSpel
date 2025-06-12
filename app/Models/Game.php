<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['status', 'winner_id', 'current_turn'];

    public function winner() {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function currentPlayer() {
        return $this->belongsTo(User::class, 'current_turn');
    }

    public function players() {
        return $this->hasMany(GamePlayer::class);
    }

    public function cells() {
        return $this->hasMany(Cell::class);
    }
}
