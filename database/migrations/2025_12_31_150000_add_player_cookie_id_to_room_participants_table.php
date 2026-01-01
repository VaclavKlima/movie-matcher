<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_participants', function (Blueprint $table) {
            $table->string('player_cookie_id', 64)->nullable()->after('session_id');
            $table->index('player_cookie_id');
            $table->unique(['room_id', 'player_cookie_id']);
        });
    }

    public function down(): void
    {
        Schema::table('room_participants', function (Blueprint $table) {
            $table->dropUnique(['room_id', 'player_cookie_id']);
            $table->dropIndex(['player_cookie_id']);
            $table->dropColumn('player_cookie_id');
        });
    }
};
