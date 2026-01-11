<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, delete any corrupted records with genre_id = 0 or NULL
        DB::table('room_participant_genre_preferences')
            ->where('genre_id', '<=', 0)
            ->orWhereNull('genre_id')
            ->delete();

        // Add CHECK constraint to prevent genre_id = 0 or negative values in the future
        // Note: MySQL 8.0.16+ supports CHECK constraints
        DB::statement('ALTER TABLE room_participant_genre_preferences ADD CONSTRAINT genre_id_must_be_positive CHECK (genre_id > 0)');
    }

    public function down(): void
    {
        // Drop the CHECK constraint if rolling back
        DB::statement('ALTER TABLE room_participant_genre_preferences DROP CHECK genre_id_must_be_positive');
    }
};
