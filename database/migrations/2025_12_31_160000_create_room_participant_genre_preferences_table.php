<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_participant_genre_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->string('preference', 10);
            $table->timestamps();

            $table->unique(['room_participant_id', 'genre_id'], 'rpgp_participant_genre_unique');
            $table->index(['room_participant_id', 'preference'], 'rpgp_participant_preference_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_participant_genre_preferences');
    }
};
