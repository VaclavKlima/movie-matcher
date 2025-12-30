<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table): void {
            $table->unsignedInteger('film_popularity_rank')->nullable()->after('film_rank');
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table): void {
            $table->dropColumn('film_popularity_rank');
        });
    }
};
