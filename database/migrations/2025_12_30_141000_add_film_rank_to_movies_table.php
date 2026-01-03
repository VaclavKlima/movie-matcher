<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: film rank is not used with TMDB data.
    }

    public function down(): void
    {
        // No-op.
    }
};
