<?php

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('movie_genre', function (Blueprint $table) {
            $table->foreignIdFor(Movie::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Genre::class)->constrained()->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('genres');
        Schema::dropIfExists('movie_genre');
    }
};
