<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('current_movie_id')
                ->nullable()
                ->constrained('movies')
                ->nullOnDelete()
                ->after('started_at');
            $table->foreignId('matched_movie_id')
                ->nullable()
                ->constrained('movies')
                ->nullOnDelete()
                ->after('current_movie_id');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('matched_movie_id');
            $table->dropConstrainedForeignId('current_movie_id');
        });
    }
};
