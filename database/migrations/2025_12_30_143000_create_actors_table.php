<?php

use App\Models\Actor;
use App\Models\Movie;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('movie_actor', function (Blueprint $table) {
            $table->foreignIdFor(Movie::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Actor::class)->constrained()->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movie_actor');
        Schema::dropIfExists('actors');
    }
};
