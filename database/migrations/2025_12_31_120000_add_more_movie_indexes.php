<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movie_votes', function (Blueprint $table): void {
            $table->index(
                ['room_id', 'decision', 'movie_id'],
                'movie_votes_room_decision_movie'
            );
        });

        Schema::table('movie_actor', function (Blueprint $table): void {
            $table->index(['movie_id', 'actor_id'], 'movie_actor_movie_actor');
            $table->index(['actor_id', 'movie_id'], 'movie_actor_actor_movie');
        });
    }

    public function down(): void
    {
        Schema::table('movie_votes', function (Blueprint $table): void {
            $table->dropIndex('movie_votes_room_decision_movie');
        });

        Schema::table('movie_actor', function (Blueprint $table): void {
            $table->dropIndex('movie_actor_movie_actor');
            $table->dropIndex('movie_actor_actor_movie');
        });
    }
};
