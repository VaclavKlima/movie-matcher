<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->string('imdb_id')->nullable();
            $table->string('name');
            $table->string('original_title')->nullable();
            $table->string('original_language', 10)->nullable();
            $table->string('poster_url')->nullable();
            $table->string('backdrop_url')->nullable();
            $table->string('country')->nullable();
            $table->year('year')->nullable();
            $table->string('duration')->nullable();
            $table->text('description')->nullable();
            $table->decimal('vote_average', 4, 1)->nullable();
            $table->unsignedInteger('vote_count')->nullable();
            $table->decimal('popularity', 10, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movies');
    }
};
