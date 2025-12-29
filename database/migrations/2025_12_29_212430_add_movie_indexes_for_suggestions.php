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
        Schema::table('movie_votes', function (Blueprint $table) {
            $table->index(['room_id', 'decision', 'room_participant_id', 'movie_id'], 'movie_votes_room_decision_participant_movie_idx');
            $table->index(['room_id', 'movie_id'], 'movie_votes_room_movie_idx');
        });

        Schema::table('movie_genre', function (Blueprint $table) {
            $table->index(['movie_id', 'genre_id'], 'movie_genre_movie_genre_idx');
            $table->index(['genre_id', 'movie_id'], 'movie_genre_genre_movie_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie_votes', function (Blueprint $table) {
            $table->dropIndex('movie_votes_room_decision_participant_movie_idx');
            $table->dropIndex('movie_votes_room_movie_idx');
        });

        Schema::table('movie_genre', function (Blueprint $table) {
            $table->dropIndex('movie_genre_movie_genre_idx');
            $table->dropIndex('movie_genre_genre_movie_idx');
        });
    }
};
