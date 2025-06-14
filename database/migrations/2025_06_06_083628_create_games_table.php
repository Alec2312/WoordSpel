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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opponent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('ongoing');
            $table->string('current_player_color')->default('Blue');
            $table->json('board_state')->nullable();
            $table->string('message')->nullable()->default('');
            $table->integer('guest_player_score')->default(0);
            $table->timestamps();
        });
        // BELANGRIJK: GEEN ANDERE TABLE CREATIES HIER! ALLEEN games.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};

