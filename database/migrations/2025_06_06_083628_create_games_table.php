<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // opponent_id kan NIET meer nullable zijn, omdat er altijd een geregistreerde opponent is.
            // Dit kan echter een probleem veroorzaken als je bestaande games hebt met opponent_id = NULL.
            // Voor nu laten we het nullable als je bestaande data hebt, maar conceptueel is het nu altijd gevuld.
            // Als je een frisse database start, kun je ->nullable() hier weghalen.
            $table->foreignId('opponent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('board_state');
            $table->string('current_player_color')->default('Blue');
            $table->string('status')->default('ongoing');
            $table->string('message')->nullable();
            // `guest_player_score` is niet aanwezig
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
