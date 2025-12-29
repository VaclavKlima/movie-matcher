<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('session_id')->index();
            $table->string('name')->nullable();
            $table->string('avatar', 32)->default('popcorn');
            $table->boolean('is_host')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_participants');
    }
};
